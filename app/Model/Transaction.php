<?php

namespace Acelle\Model;

use Illuminate\Database\Eloquent\Model;
use Acelle\Library\Traits\HasUid;
use Twilio\TwiML\Voice\Pay;

class Transaction extends Model
{
    use HasUid;
    protected $connection = 'mysql';

    // wait status
    public const STATUS_PENDING = 'pending';
    public const STATUS_FAILED = 'failed';
    public const STATUS_SUCCESS = 'success';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status', 'payment_method_id', 'error', 'allow_manual_review'
    ];

    /**
     * Invoice.
     */
    public function invoice()
    {
        return $this->belongsTo('Acelle\Model\Invoice');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    /**
     * Is failed.
     */
    public function isFailed()
    {
        return $this->status == self::STATUS_FAILED;
    }

    public function isPending()
    {
        return $this->status == self::STATUS_PENDING;
    }

    /**
     * Set as success.
     */
    public function setSuccess()
    {
        $this->status = self::STATUS_SUCCESS;
        $this->save();
    }

    // Transaction that needs admin review
    public function allowManualReview()
    {
        return $this->allow_manual_review;
    }

    public static function scopePending($query)
    {
        $query = $query->where('status', Transaction::STATUS_PENDING);
    }

    public function approve()
    {
        // for only new invoice
        if (!$this->invoice->isNew()) {
            throw new \Exception("Trying to approve an transaction that its invoice is not NEW (Invoice ID: {$this->id}, status: {$this->status}");
        }

        // fulfill invoice
        $this->invoice->mapType()->paySuccess();
    }
}
