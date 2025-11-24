<?php

namespace Acelle\Library\SendingServer;

use Acelle\Model\SendingDomain;

interface DomainVerificationInterface
{
    /*
     * Structure of the return value:
     *
     *
     *

        return [
            'identity' => [
                'name' => '_amazonses.your-domain.live',
                'type' => 'CNAME',
                'value' => 'IBasT4ddDMUsPcd3hchW9b7uQa01xFDorqkbELxXg+o=',
            ],

            'dkim' => [
                [ 'name' => '3hsorb._domainkey.your-domain.live', 'type' => 'CNAME', 'value' => '3hsorb.dkim.amazonses.com' ],
                [ 'name' => 'rjmo7u._domainkey.your-domain.live', 'type' => 'CNAME', 'value' => 'rjmo7u.dkim.amazonses.com' ],
                [ 'name' => 'cjaydq._domainkey.your-domain.live', 'type' => 'CNAME', 'value' => 'cjaydq.dkim.amazonses.com' ],
            ],

            'spf' => [
                [ 'name' => '@', 'value' => 'v=spf1 a mx include:_spf.smtp-service.com ~all', 'type' => 'TXT' ],
            ],

            'dmarc' => [ 'name' => '_dmarc', 'type' => 'TXT', 'value' => 'v=DMARC1;p=none;' ],

            'results' => [
                'identity' => false,
                'dkim' => false,
                'spf' => false,
            ],
        ]
    */
    public function addDomain(string $domain): array;

    /*
     * Return: an array of boolean value indicating whether or not the related check is true or false
     * Sample: [ bool $identity, bool $dkim, bool $spf, bool $dmarc, bool $finalStatus ]
     */
    public function checkDomainVerificationStatus(SendingDomain $domain): array;

    /*
     * Sometimes it is needed to show up some information to users
     *
     */
    public function getServiceName(): string;
}
