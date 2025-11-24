<?php

namespace Acelle\Http\Controllers;

use Acelle\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Acelle\Library\Facades\Billing;
use Acelle\Model\Invoice;
use Acelle\Model\PaymentGateway;
use Acelle\Model\PaymentMethod;

class CheckoutController extends Controller
{
    public function cart(Request $request, $invoice_uid)
    {
        // init
        $customer = $request->user()->customer;
        $invoice = Invoice::findByUid($invoice_uid);

        if (!$invoice) {
            return view('somethingWentWrong', ['message' => 'There are no new sender ID invoice!']);
        }

        return view('checkout.cart', [
            'invoice' => $invoice,
        ]);
    }

    public function billingAddress(Request $request, $invoice_uid)
    {
        // init
        $customer = $request->user()->customer;
        $invoice = Invoice::findByUid($invoice_uid);

        if (!$invoice) {
            return view('somethingWentWrong', ['message' => 'There are no new sender ID invoice!']);
        }

        // get default billing address from customer
        $billingAddress = $customer->getDefaultBillingAddress();
        if ($billingAddress) {
            $invoice->fillBillingAddressNotReplace($billingAddress);
        }

        // Save posted data
        if ($request->isMethod('post')) {
            $validator = $invoice->updateBillingInformation($request->all());

            // redirect if fails
            if ($validator->fails()) {
                return response()->view('checkout.billingAddress', [
                    'invoice' => $invoice,
                    'errors' => $validator->errors(),
                ], 400);
            }

            // Khúc này customer cập nhật thông tin billing information cho lần tiếp theo
            $customer->updateBillingInformationFromInvoice($invoice);

            $request->session()->flash('alert-success', trans('messages.billing_address.updated'));

            // return to subscription
            return redirect()->action('CheckoutController@payment', [
                'invoice_uid' => $invoice->uid,
            ]);
        }

        return view('checkout.billingAddress', [
            'invoice' => $invoice,
        ]);
    }

    public function payment(Request $request, $invoice_uid)
    {
        // init
        $customer = $request->user()->customer;
        $invoice = Invoice::findByUid($invoice_uid);

        if (!$invoice) {
            return view('somethingWentWrong', ['message' => 'There are no new sender ID invoice!']);
        }

        // not have billing address
        if (!$invoice->hasBillingInformation()) {
            return view('somethingWentWrong', ['message' => 'Invoice do not has billing address!']);
        }

        return view('checkout.payment', [
            'invoice' => $invoice,
        ]);
    }

    public function orderBox(Request $request, $invoice_uid)
    {
        // init
        $customer = $request->user()->customer;
        $invoice = Invoice::findByUid($invoice_uid);

        if (!$invoice) {
            return view('somethingWentWrong', ['message' => 'There are no new sender ID invoice!']);
        }

        // gateway fee
        if ($request->payment_gateway_id) {
            $paymentGateway = \Acelle\Model\PaymentGateway::findByUid($request->payment_gateway_id);

            // update invoice fee if trial and gatewaye need minimal fee for auto billing
            $invoice->updatePaymentServiceFee($paymentGateway->getService());
        }

        return view('checkout.orderBox', [
            'bill' => $invoice->mapType()->getBillingInfo(),
            'invoice' => $invoice,
        ]);
    }

    public function checkout(Request $request, $invoice_uid)
    {
        // init
        $invoice = Invoice::findByUid($invoice_uid);

        if (!$invoice) {
            return view('somethingWentWrong', ['message' => 'There are no new invoice!']);
        }

        // not have billing address
        if (!$invoice->hasBillingInformation()) {
            return view('somethingWentWrong', ['message' => 'Invoice do not has billing address!']);
        }

        // always update invoice price from plan
        $invoice->mapType()->refreshPrice();

        // set return url
        switch ($invoice->type) {
            case \Acelle\Model\InvoiceNewSubscription::TYPE_NEW_SUBSCRIPTION:
            case \Acelle\Model\InvoiceRenewSubscription::TYPE_RENEW_SUBSCRIPTION:
            case \Acelle\Model\InvoiceChangePlan::TYPE_CHANGE_PLAN:
                $subscription = $invoice->mapType()->subscription;
                switch ($subscription->plan->type) {
                    case \Acelle\Model\PlanGeneral::TYPE_GENERAL:
                        Billing::setReturnUrl(action('SubscriptionController@index'));
                        break;
                    case \Acelle\Model\PlanVerification::TYPE_VERIFICATION:
                        Billing::setReturnUrl(action('EmailVerificationPlanController@index'));
                        break;
                    default:
                        throw new \Exception("Plan type #{$subscription->plan->type} not found!");
                }
                break;
            case \Acelle\Model\InvoiceEmailVerificationCredits::TYPE_EMAIL_VERIFICATION_CREDITS:
                Billing::setReturnUrl(action('EmailVerificationPlanController@index'));
                break;
            case \Acelle\Model\InvoiceSendingCredits::TYPE_SENDING_CREDITS:
                Billing::setReturnUrl(action('SendingCreditPlanController@index'));
                break;
            default:
                throw new \Exception("Invoice type #{$invoice->type} not found!");
        }

        // free plan. No charge
        if ($invoice->total() == 0) {
            $invoice->paySuccess();

            return redirect()->away(Billing::getReturnUrl());
        }

        // if auto charge
        if ($request->payment_method_id) {
            // payment method
            $paymentMethod = PaymentMethod::findByUid($request->payment_method_id);

            // auto charge
            $invoice->autoCharge($paymentMethod);

            // redirect to invoice page
            return redirect()->away(Billing::getReturnUrl());
        } else {
            // get payment gateway
            $paymentGateway = PaymentGateway::findByUid($request->payment_gateway_id);

            // redirect to service checkout
            return redirect()->away($paymentGateway->getCheckoutUrl($invoice));
        }
    }

    public function cancel(Request $request, $invoice_uid)
    {
        // init
        $customer = $request->user()->customer;
        $invoice = Invoice::findByUid($invoice_uid);

        if (!$invoice) {
            return view('somethingWentWrong', ['message' => "Can not find the invoice #{$invoice_uid}"]);
        }

        // cancel invoice
        $invoice->cancel();

        return redirect()->back();
    }
}
