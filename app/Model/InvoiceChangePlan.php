<?php

namespace Acelle\Model;

use Acelle\Model\Invoice;
use Acelle\Library\Facades\SubscriptionFacade;
use Acelle\Library\Contracts\PlanInterface;

class InvoiceChangePlan extends Invoice
{
    protected $connection = 'mysql';

    protected $table = 'invoices';

    public const TYPE_CHANGE_PLAN = 'change_plan';

    public function subscription()
    {
        return $this->belongsTo('Acelle\Model\Subscription');
    }

    public function newPlan()
    {
        return $this->belongsTo('Acelle\Model\Plan', 'new_plan_id');
    }

    /**
     * Process invoice.
     *
     * @return void
     */
    public function process()
    {
        // Xoá NEW renew invoice hiện tại nếu có
        if ($this->subscription->getItsOnlyUnpaidRenewInvoice()) {
            $this->subscription->getItsOnlyUnpaidRenewInvoice()->delete();
        }

        // change plan
        $this->subscription->changePlan($this->newPlan);

        // Handle business for different types of plan
        $this->getPlan()->handlePlanChangedSuccess($this->subscription);

        // Logging
        SubscriptionFacade::log($this->subscription, SubscriptionLog::TYPE_PAY_SUCCESS, $this->uid, [
            'amount' => $this->total(),
        ]);
    }

    public function getPlan(): PlanInterface
    {
        return $this->subscription->plan->mapType();
    }

    /**
     * Get billing info.
     *
     * @return void
     */
    public function getBillingInfo()
    {
        $chargeInfo = trans('messages.bill.charge_now');

        return $this->getBillingInfoBase($chargeInfo, $this->newPlan);
    }

    public function checkoutAfterPayFailed($error)
    {
        SubscriptionFacade::log($this->subscription, SubscriptionLog::TYPE_PAY_FAILED, $this->uid, [
            'amount' => $this->total(),
            'error' => $error,
        ]);
    }

    public function beforeCancel()
    {
        SubscriptionFacade::log($this->subscription, SubscriptionLog::TYPE_CANCEL_INVOICE, $this->uid, [
            'amount' => $this->total(),
        ]);
    }

    public function refreshPrice()
    {
    }
}
