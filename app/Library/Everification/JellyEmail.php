<?php

namespace Acelle\Library\Everification;

use Exception;
use GuzzleHttp\Client;
use Acelle\Library\Contracts\VerifyInterface;

class JellyEmail implements VerifyInterface
{
    protected $url;
    protected $apiToken;
    protected $client;

    protected $map = [
        'valid' => 'deliverable',
        'invalid' => 'undeliverable',
        'risky' => 'risky',
    ];

    public function __construct($url, $apiToken)
    {
        $this->url = rtrim($url, '/');
        $this->apiToken = $apiToken;
    }

    public function getClient()
    {
        if ($this->client) {
            return $this->client;
        }

        $this->client = new Client([
            'verify' => false,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$this->apiToken}",
            ],
        ]);

        return $this->client;
    }

    public function verify($email)
    {
        $endpoint = "{$this->url}/api/verify-email";
        
        try {
            $response = $this->getClient()->request('POST', $endpoint, [
                'json' => ['email' => $email],
            ]);
            
            $result = $this->parseResult($response);
            
            return [$result, (string)$response->getBody()];
        } catch (\Exception $e) {
            // Check if it's a known error response
            if ($e instanceof \GuzzleHttp\Exception\BadResponseException) {
                $response = $e->getResponse();
                $body = (string) $response->getBody();
                throw new Exception("JellyEmail API Error ({$response->getStatusCode()}): {$body}");
            }
            
            throw new Exception("JellyEmail connection error: " . $e->getMessage());
        }
    }

    public function parseResult($response)
    {
        $raw = (string)$response->getBody();
        $json = json_decode($raw, true);

        if (empty($json) || !isset($json['status'])) {
            throw new Exception('Unexpected response from JellyEmail: ' . $raw);
        }

        $status = $json['status'];

        if (array_key_exists($status, $this->map)) {
            return $this->map[$status];
        }

        return 'unknown';
    }

    public function isBulkVerifySupported(): bool
    {
        return false;
    }
}
