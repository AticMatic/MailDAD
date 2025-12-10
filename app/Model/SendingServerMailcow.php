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
        // Check if domain exists
        try {
            $response = $this->client()->get('/api/v1/get/domain/' . $domain);
            $body = json_decode($response->getBody(), true);
            if (!empty($body)) {
                return; // Domain already exists
            }
        } catch (Exception $e) {
            // Ignore error if domain likely doesn't exist
        }

        // Add domain
        $response = $this->client()->post('/api/v1/add/domain', [
            'json' => [
                'domain' => $domain,
                'description' => 'Added by MailDad',
                'active' => 1,
                'aliases' => 400,
                'mailboxes' => 10,
                'def_new_mailbox_quota' => 3072,
            ]
        ]);

        $body = json_decode($response->getBody(), true);
        if (isset($body['type']) && $body['type'] == 'error') {
             throw new Exception("Failed to add domain to Mailcow: " . $body['msg']);
        }
        
        // Auto-generate DKIM
        $this->client()->post('/api/v1/add/dkim', [
             'json' => [
                 'domain' => $domain,
                 'key_size' => 2048,
                 'selector' => 'dkim',
             ]
        ]);
    }
    
    public function checkDomainVerificationStatus($domainModel)
    {
        $domain = $domainModel->name;
        $dkim = false;
        $spf = false; // Mailcow doesn't explicitly check SPF for you in the same way, but let's assume valid if domain exists? No, we should check DNS.
        // Actually, the parent `SendingDomain` model has `verifySpf` and `verifyDkim` methods which check DNS.
        // All we need to do here is return the EXPECTED values for validation.
        
        // HOWEVER, `SendingDomain::verify()` logic says:
        // if associated with sending server -> call mapType()->checkDomainVerificationStatus($this)
        // This function returns [identity, dkim, spf, dmarc, finalStatus]
        
        // Identity (TXT) verification might not be needed for Mailcow if we own the server, 
        // but typically we still want to verify ownership.
        // For self-hosted, maybe we assume identity is true if it was added successfully? 
        // Or we can rely on standard DNS checks.
        
        // Let's retrieve DKIM from Mailcow to populate the `SendingDomain` model
        try {
            $response = $this->client()->get('/api/v1/get/dkim/' . $domain);
            $body = json_decode($response->getBody(), true);
            // Mailcow returns the public key part
            if (!empty($body)) {
                 // Update domain model DKIM if missing (optional, but good for UI)
                 // content usually looks like "v=DKIM1; k=rsa; p=MIIBIjANBg..."
                 // The `SendingDomain` expects `dkim_public`
            }
        } catch (Exception $e) {
            //
        }

        // For now, we delegate to standard DNS verification
        // Because checking verification status is about "Did the user update their DNS?"
        // not "Does the domain exist on Mailcow?".
        
        $identityVerified = $domainModel->verifyIdentity();
        $dkimVerified = $domainModel->getDomain()->verifyDkim();
        $spfVerified = $domainModel->verifySpf($this->host); // Use mailcow host IP/Domain for SPF check
        
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
