<?php

namespace Acelle\Model;

use Illuminate\Database\Eloquent\Model;
use Acelle\Library\Traits\HasUid;

class PaymentMethod extends Model
{
    use HasUid;

    protected $fillable = [
        'unique_id',
        'autobilling_data',
        'more_info',
        'payment_gateway_id',
        'can_auto_charge',
    ];

    public function scopeCanAutoCharge($query)
    {
        return $query->where('can_auto_charge', true);
    }

    public function paymentGateway()
    {
        return $this->belongsTo(PaymentGateway::class, 'payment_gateway_id');
    }

    public function autoCharge(Invoice $invoice)
    {
        return $this->paymentGateway->getService()->autoCharge($invoice, $this);
    }

    public function getInformation()
    {
        return $this->more_info;
    }

    // get method title
    public function getMethodTitle()
    {
        return $this->paymentGateway->getService()->getMethodTitle($this->getAutobillingData());
    }

    // get method info
    public function getMethodInfo()
    {
        return $this->paymentGateway->getService()->getMethodInfo($this->getAutobillingData());
    }

    public function getAutobillingData()
    {
        return json_decode($this->autobilling_data, true);
    }
}
