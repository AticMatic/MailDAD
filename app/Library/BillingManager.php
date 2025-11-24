<?php

namespace Acelle\Library;

use Exception;
use Acelle\Model\Setting;

class BillingManager
{
    protected $gateways = [];

    public function __construct()
    {
    }

    public function setReturnUrl($url)
    {
        session()->put('billingReturnUrl', $url);
    }

    public function getReturnUrl()
    {
        return session()->get('billingReturnUrl', url('/'));
    }

    public function register($type, $name, $description)
    {
        if ($this->isGatewayRegistered($type)) {
            throw new Exception(sprintf('Payment gateway type "%s" is already registered', $type));
        }

        $this->gateways[$type] = [
            'type' => $type,
            'name' => $name,
            'description' => $description,
        ];
    }

    public function getGateways(): array
    {
        return $this->gateways;
    }

    public function isGatewayRegistered($type)
    {
        return array_key_exists($type, $this->gateways);
    }
}
