<?php

namespace Acelle\Model;

use Acelle\Model\Invoice;
use Acelle\Library\Facades\SubscriptionFacade;
use Exception;
use Acelle\Library\Facades\Hook;
use Acelle\Library\Contracts\PlanInterface;

class InvoiceNewSubscription extends Invoice
{
    protected $connection = 'mysql';

    protected $table = 'invoices';

    public const TYPE_NEW_SUBSCRIPTION = 'new_subscription';

    public function subscription()
    {
        return $this->belongsTo('Acelle\Model\Subscription');
    }

    /**
     * Process invoice.
     *
     * @return void
     */
    public function process()
    {
        // activate sub
        $this->subscription->activate();

        // Handle business for different types of plan
        $this->getPlan()->handleSubscriptionActivatedSuccess($this->subscription);

        // @todo: move the log below to the paySuccess() method.
        // Nên để ở đây vì paySuccess là hàm chung cho invoice
        // Vì dụ: invoice ở đầy có thể là InvoiceTopUp => không liên quan đến subscription
        SubscriptionFacade::log($this->subscription, SubscriptionLog::TYPE_PAY_SUCCESS, $this->uid, [
            'amount' => $this->total(),
        ]);

        // Execute registered hooks
        Hook::execute('new_subscription', [$this->subscription]);
    }

    /**
     * Get billing info.
     *
     * @return void
     */
    public function getBillingInfo()
    {
        $chargeInfo = trans('messages.bill.charge_now');
        $plan = $this->getPlan();

        return $this->getBillingInfoBase($chargeInfo, $plan);
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
        throw new \Exception('Sub đang new thì không thể xóa invoice!');
    }

    public function refreshPrice()
    {
        if ($this->isPaid()) {
            throw new \Exception("Can not change paid invoice price!");
        }

        $invoiceItem = $this->invoiceItems()->first(); // subscription plan always has 1 invoice item (design)

        // if has trial => set price = 0
        if ($this->getPlan()->hasTrial() && $invoiceItem->amount != 0) {
            $invoiceItem->amount = 0;
            $invoiceItem->save();
        }

        // if not trial and price updated
        elseif (!$this->getPlan()->hasTrial() && $invoiceItem->amount != $this->getPlan()->getPrice()) {
            $invoiceItem->amount = $this->getPlan()->getPrice();
            $invoiceItem->save();
        }
    }

    public function getPlan(): PlanInterface
    {
        return $this->subscription->plan->mapType();
    }

    public function updatePaymentServiceFee($gateway)
    {
        // trường hợp init invoice có free trial và setting không require card thì fee = 0. Overide cái điều kiện khác.
        if (\Acelle\Model\Setting::get('not_require_card_for_trial') == 'yes' &&
            $this->getPlan()->hasTrial()
        ) {
            $this->setFee(0);

            return;
        }

        // Mặc định
        return parent::updatePaymentServiceFee($gateway);
    }
}
