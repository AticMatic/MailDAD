<?php

namespace Acelle\Http\Controllers;

use Illuminate\Http\Request;
use Acelle\Model\Subscription;

class SubscriptionPopupController extends Controller
{
    public function index(Request $request)
    {
        $subscription = Subscription::findByUid($request->uid);

        return view('subscription_popup.index', [
            'subscription' => $subscription,
        ]);
    }

    public function disableRecurring(Request $request)
    {
        if (isSiteDemo()) {
            return response()->json(["message" => trans('messages.operation_not_allowed_in_demo')], 404);
        }

        // init
        $subscription = Subscription::findByUid($request->uid);

        if ($request->user()->customer->can('disableRecurring', $subscription)) {
            $subscription->disableRecurring();
        }

        // return
        return response()->json([
            'status' => 'success',
            'message' => trans('messages.subscription.disabled_recurring'),
        ]);
    }

    public function enableRecurring(Request $request)
    {
        if (isSiteDemo()) {
            return response()->json(["message" => trans('messages.operation_not_allowed_in_demo')], 404);
        }

        // init
        $subscription = Subscription::findByUid($request->uid);

        if ($request->user()->customer->can('enableRecurring', $subscription)) {
            $subscription->enableRecurring();
        }

        // return
        return response()->json([
            'status' => 'success',
            'message' => trans('messages.subscription.enabled_recurring'),
        ]);
    }

    public function cancelNow(Request $request)
    {
        if (isSiteDemo()) {
            return response()->json(["message" => trans('messages.operation_not_allowed_in_demo')], 404);
        }

        // init
        $subscription = Subscription::findByUid($request->uid);

        if ($request->user()->customer->can('cancelNow', $subscription)) {
            $subscription->cancelNow();
        }

        // return
        return response()->json([
            'status' => 'success',
            'message' => trans('messages.subscription.cancelled_now'),
        ]);
    }
}
