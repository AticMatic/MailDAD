<?php

namespace Acelle\Model;

use Acelle\Library\Log as MailLog;
use GuzzleHttp\Client;
use Exception;

class SendingServerMailcow extends SendingServerSmtp
{
    protected $table = 'sending_servers';

    /**
     * Send the provided message.
     *
     * @return bool
     *
     * @param message
     */
    public function send($message, $params = array())
    {
        return parent::send($message, $params);
    }

    public function client()
    {
        return new Client([
            'base_uri' => 'https://' . $this->host,
            'headers' => [
                'X-API-Key' => $this->api_key,
                'Content-Type' => 'application/json',
            ],
            'verify' => false,
        ]);
    }

    public function test()
    {
        // Test SMTP
        parent::test();

        // Test API
        try {
            $response = $this->client()->get('/api/v1/get/domain/all');
            if ($response->getStatusCode() != 200) {
                throw new Exception("Mailcow API connection failed: " . $response->getReasonPhrase());
            }
        } catch (Exception $e) {
            throw new Exception("Mailcow API connection failed: " . $e->getMessage());
        }

        return true;
    }

    public function allowVerifyingOwnDomainsRemotely()
    {
        return true;
    }
    
    public function allowOtherSendingDomains()
    {
        return true;
    }

    public function addDomain($domain)
    {
        MailLog::info("SendingServerMailcow::addDomain - Starting for domain: " . $domain);
        
        // Check if domain exists
        try {
            $response = $this->client()->get('/api/v1/get/domain/' . $domain);
            $body = json_decode($response->getBody(), true);
            if (!empty($body)) {
                // Domain exists
            } else {
                 // Add domain
                 $this->client()->post('/api/v1/add/domain', [
                    'json' => [
                        'domain' => $domain,
                        'description' => 'Added by MailDad',
                        'active' => 1,
                        'aliases' => 400,
                        'mailboxes' => 10,
                        'def_new_mailbox_quota' => 3072,
                    ]
                ]);
            }
        } catch (Exception $e) {
             // Add domain if check failed (assuming 404)
             try {
                $this->client()->post('/api/v1/add/domain', [
                    'json' => [
                        'domain' => $domain,
                        'description' => 'Added by MailDad',
                        'active' => 1,
                        'aliases' => 400,
                        'mailboxes' => 10,
                        'def_new_mailbox_quota' => 3072,
                    ]
                ]);
             } catch (Exception $e2) {
                 // Ignore if it's already there or handle error
             }
        }
        
        // Auto-generate DKIM (idempotent usually)
        try {
            $this->client()->post('/api/v1/add/dkim', [
                 'json' => [
                     'domain' => $domain,
                     'key_size' => 2048,
                     'selector' => 'dkim',
                 ]
            ]);
        } catch (Exception $e) {
            // Ignore if key exists
        }

        // Fetch DKIM Key
        $dkimValue = '';
        try {
             $response = $this->client()->get('/api/v1/get/dkim/' . $domain);
             $body = json_decode($response->getBody(), true);
             // Mailcow returns 'dkim_txt' usually
             if (isset($body['dkim_txt'])) {
                 $dkimValue = $body['dkim_txt'];
             }
        } catch (Exception $e) {
            MailLog::error("Failed to fetch DKIM for Mailcow domain: " . $e->getMessage());
        }

        // Prepare Tokens
        $identityToken = base64_encode(md5(trim('SALT!'.$domain)));
        $spfValue = 'v=spf1 include:' . $this->host . ' -all';

        return [
            'identity' => [
                'type' => 'TXT',
                'name' => $domain,
                'value' => $identityToken,
            ],
            'dkim' => [
                [
                    'type' => 'TXT',
                    'name' => 'dkim._domainkey.' . $domain,
                    'value' => $dkimValue,
                ]
            ],
            'spf' => [
               'type' => 'TXT',
               'name' => $domain,
               'value' => $spfValue,
            ],
            'results' => [
                'identity' => false,
                'dkim' => false,
                'spf' => false,
            ]
        ];
    }
    
    public function checkDomainVerificationStatus($domainModel)
    {
        // Update local model with DKIM key from Mailcow to ensure verification works
        if (empty($domainModel->dkim_public)) {
             try {
                 $response = $this->client()->get('/api/v1/get/dkim/' . $domainModel->name);
                 $body = json_decode($response->getBody(), true);
                 if (isset($body['pub_key'])) {
                     $domainModel->dkim_public = $body['pub_key'];
                     $domainModel->dkim_selector = 'dkim';
                     // Extract private key? Mailcow might not provide it easily via this endpoint or we don't need it if relaying?
                     // If we are sending VIA Mailcow SMTP, we don't need the private key locally as Mailcow signs it.
                     // But Standard Acelle SendingDomain verification might require keys?
                     // createFromArray calls generateDkimKeys which fills both.
                     // Here we have remote signing.
                     // So we just save the public key for DNS check.
                     $domainModel->save();
                 }
             } catch (Exception $e) {
                 //
             }
        }

        $identityVerified = $domainModel->verifyIdentity();
        $dkimVerified = $domainModel->getDomain()->verifyDkim();
        $spfVerified = $domainModel->verifySpf($this->host);
        
        return [$identityVerified, $dkimVerified, $spfVerified, true, ($identityVerified && $dkimVerified)];
    }
    
    // Add mailbox if needed
    public function createMailbox($email, $password, $name)
    {
        list($local, $domain) = explode('@', $email);
        
        $response = $this->client()->post('/api/v1/add/mailbox', [
            'json' => [
                'local_part' => $local,
                'domain' => $domain,
                'password' => $password,
                'name' => $name,
                'active' => 1,
                'quota' => 3072,
            ]
        ]);
        
        $body = json_decode($response->getBody(), true);
         if (isset($body['type']) && $body['type'] == 'error') {
             // throw new Exception("Failed to create mailbox: " . $body['msg']);
             // Maybe it already exists?
             return false;
        }
        return true;
    }
}
