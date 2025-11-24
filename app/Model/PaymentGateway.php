<?php

namespace Acelle\Model;

use Illuminate\Database\Eloquent\Model;
use Acelle\Library\Traits\HasUid;
use Acelle\Library\Facades\Billing;
use Acelle\Cashier\Services\StripePaymentGateway;
use Acelle\Cashier\Services\OfflinePaymentGateway;
use Acelle\Cashier\Services\BraintreePaymentGateway;
use Acelle\Cashier\Services\PaystackPaymentGateway;
use Acelle\Cashier\Services\PaypalPaymentGateway;
use Acelle\Cashier\Services\RazorpayPaymentGateway;

class PaymentGateway extends Model
{
    use HasUid;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public function paymentMethods()
    {
        return $this->hasMany('Acelle\Model\PaymentMethod', 'payment_gateway_id');
    }

    public static function scopeSearch($query, $keyword)
    {
        $keyword = strtolower(trim($keyword));

        // search by keyword
        if ($keyword) {
            $query =  $query->whereRaw('LOWER(name) LIKE ? OR LOWER(description) LIKE ?', '%'.$keyword.'%', '%'.$keyword.'%');
        }
    }

    public static function newDefault($type)
    {
        $paymentGateway = new static();
        $paymentGateway->type = $type;
        $paymentGateway->status = self::STATUS_ACTIVE;

        // Default name and description
        $service = Billing::getGateways()[$paymentGateway->type];
        $paymentGateway->name = $service['name'];
        $paymentGateway->description = $service['description'];

        return $paymentGateway;
    }

    public function getGatewayDatas()
    {
        if (!$this->gatewayData) {
            return [];
        }

        return json_decode($this->gatewayData, true);
    }

    public function getGatewayData($key)
    {
        $data = $this->getGatewayDatas();

        return array_get($data, $key) ?? null;
    }

    public function savePaymentGateway($name, $description, $gatewayData)
    {
        $this->name = $name;
        $this->description = $description;
        $this->gatewayData = json_encode($gatewayData);

        // validation
        $validator = \Validator::make([
            'name' => $name,
            'description' => $description,
            'gateway_data' => $gatewayData,
        ], [
            'name' => 'required',
            'description' => 'required',
            'gateway_data' => 'required|array',
        ]);

        // errors
        if ($validator->fails()) {
            return $validator;
        }

        // save
        $this->save();

        return $validator;
    }

    public function disable()
    {
        $this->status = self::STATUS_INACTIVE;
        $this->save();
    }

    public function enable()
    {
        $this->status = self::STATUS_ACTIVE;
        $this->save();
    }

    public function isActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    public function isInactive()
    {
        return $this->status == self::STATUS_INACTIVE;
    }

    public static function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function getService()
    {
        switch ($this->type) {
            case OfflinePaymentGateway::TYPE:
                $paymentInstruction = $this->getGatewayData('payment_instruction');
                return new OfflinePaymentGateway($paymentInstruction);
            case StripePaymentGateway::TYPE:
                $publishableKey = $this->getGatewayData('publishable_key');
                $secretKey = $this->getGatewayData('secret_key');
                return new StripePaymentGateway($publishableKey, $secretKey);
            case BraintreePaymentGateway::TYPE:
                $environment = $this->getGatewayData('environment');
                $merchantId = $this->getGatewayData('merchant_id');
                $publicKey = $this->getGatewayData('public_key');
                $privateKey = $this->getGatewayData('private_key');
                return new BraintreePaymentGateway($environment, $merchantId, $publicKey, $privateKey);
            case PaystackPaymentGateway::TYPE:
                $publicKey = $this->getGatewayData('public_key');
                $secretKey = $this->getGatewayData('secret_key');
                return new PaystackPaymentGateway($publicKey, $secretKey);
            case PaypalPaymentGateway::TYPE:
                $environment = $this->getGatewayData('environment');
                $clientId = $this->getGatewayData('client_id');
                $secret = $this->getGatewayData('secret');
                return new PaypalPaymentGateway($environment, $clientId, $secret);
            case RazorpayPaymentGateway::TYPE:
                $keyId = $this->getGatewayData('key_id');
                $keySecret = $this->getGatewayData('key_secret');
                return new RazorpayPaymentGateway($keyId, $keySecret);
            default:
                throw new \Exception('Unsupported payment gateway type: ' . $this->type);
        }
    }

    public function getCheckoutUrl($invoice)
    {
        return $this->getService()->getCheckoutUrl($invoice, $this->uid);
    }
}
