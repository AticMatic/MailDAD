<?php

namespace Acelle\Http\Controllers;

use Illuminate\Http\Request;
use Acelle\Model\PlanVerification;
use Acelle\Library\Facades\SubscriptionFacade;
use Acelle\Model\SubscriptionLog;

class PlanVerificationController extends Controller
{
    public function select(Request $request)
    {
        $plans = PlanVerification::verification()->available()->orderBy('price', 'asc')->get();

        return view('plan_verifications.select', [
            'plans' => $plans,
        ]);
    }

    public function assignPlan(Request $request)
    {
        $customer = $request->user()->customer;
        $plan = PlanVerification::findByUid($request->plan_uid);

        // assign plan
        $subscription = $customer->assignVerificationPlan($plan);

        // get init invoice
        $invoice = $subscription->getItsOnlyUnpaidInitInvoice();

        // Redirect to checkout process
        return redirect()->action('CheckoutController@billingAddress', [
            'invoice_uid' => $invoice->uid,
        ]);
    }

    public function subscriptionList(Request $request)
    {
        $subscriptions = $request->user()->customer->verificationSubscriptions()->newOrActive();

        if (isset($request->plan_uid)) {
            $plan = PlanVerification::findByUid($request->plan_uid);
            $subscriptions = $subscriptions->where('plan_id', $plan->id);
        }

        if (!empty($request->sort_order)) {
            $subscriptions = $subscriptions->orderBy($request->sort_order, $request->sort_direction);
        }

        // pagination
        $subscriptions = $subscriptions->paginate($request->per_page);

        // view
        return view('plan_verifications.subscriptionList', [
            'subscriptions' => $subscriptions,
        ]);
    }

    /**
     * Change plan.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     **/
    public function changePlan(Request $request)
    {
        $newPlan = PlanVerification::findByUid($request->plan_uid);
        $customer = $request->user()->customer;
        $subscription = $customer->getCurrentActiveVerificationSubscription();

        // Authorization
        if (!$request->user()->customer->can('changePlan', $subscription)) {
            return $this->notAuthorized();
        }

        // try {
        $changePlanInvoice = null;

        \DB::transaction(function () use ($subscription, $newPlan, &$changePlanInvoice) {
            // set invoice as pending
            $changePlanInvoice = $subscription->createChangePlanInvoice($newPlan);

            // Log
            SubscriptionFacade::log($subscription, SubscriptionLog::TYPE_CHANGE_PLAN_INVOICE, $changePlanInvoice->uid, [
                'plan' => $subscription->getPlanName(),
                'new_plan' => $newPlan->name,
                'amount' => $changePlanInvoice->total(),
            ]);
        });

        // return to subscription
        return redirect()->action('CheckoutController@billingAddress', [
            'invoice_uid' => $changePlanInvoice->uid,
        ]);
        // } catch (\Exception $e) {
        //     $request->session()->flash('alert-error', $e->getMessage());
        //     return redirect()->action('EmailVerificationPlanController@index');
        // }
    }
}
