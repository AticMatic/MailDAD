<?php

namespace Acelle\Http\Controllers\Admin;

use Acelle\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Acelle\Model\PlanVerification;

class PlanVerificationController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.plan_verifications.index');
    }

    public function list(Request $request)
    {
        // init
        $perPage    =   $request->perPage ?? 10;
        $keyword    =   $request->keyword ?? '' ;

        // Get sending servers
        $plans = PlanVerification::verification()->search($keyword);

        //sort
        if ($request->sort) {
            $plans->orderBy($request->sort['by'], $request->sort['direction']);
        }

        // pagination
        $plans = $plans->paginate($perPage);

        return view('admin.plan_verifications.list', [
            'plans'   => $plans,
            'sort_by'     => $request->sort['by'] ?? '',
            'sort_direction' => $request->sort['direction'] ?? '',
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        // init
        $plan = PlanVerification::newDefault();

        //
        return view('admin.plan_verifications.create', [
            'plan' => $plan,
        ]);
    }

    public function store(Request $request)
    {
        // init
        $plan = PlanVerification::newDefault($request->type);

        // Try to save
        $validator = $plan->saveFromParams(
            $name = $request->name,
            $description = $request->description,
            $email_verification_credits = $request->email_verification_credits,
            $price = $request->price,
            $currency_id = $request->currency_id,
            $frequency_amount = $request->frequency_amount,
            $frequency_unit = $request->frequency_unit,
            $trial_amount = $request->trial_amount,
            $trial_unit = $request->trial_unit
        );

        // if error
        if ($validator->fails()) {
            return response()->view('admin.plan_verifications.create', [
                'plan' => $plan,
                'errors' => $validator->errors(),
            ], 400);
        }

        // Send messenge
        $request->session()->flash('alert-success', trans('sms.plan_verifications.create.success'));

        // redirect
        return redirect()->action('Admin\PlanVerificationController@index');
    }

    public function edit(Request $request, $uid)
    {
        // init
        $plans = PlanVerification::findByUid($uid);

        //
        return view('admin.plan_verifications.edit', [
            'plan' => $plans,
        ]);
    }

    public function update(Request $request, $uid)
    {
        // init
        $plan = PlanVerification::findByUid($uid);

        // Try to save
        $validator = $plan->saveFromParams(
            $name = $request->name,
            $description = $request->description,
            $email_verification_credits = $request->email_verification_credits,
            $price = $request->price,
            $currency_id = $request->currency_id,
            $frequency_amount = $request->frequency_amount,
            $frequency_unit = $request->frequency_unit,
            $trial_amount = $request->trial_amount,
            $trial_unit = $request->trial_unit
        );

        // if error
        if ($validator->fails()) {
            return response()->view('admin.plan_verifications.edit', [
                'plan' => $plan,
                'errors' => $validator->errors(),
            ], 400);
        }

        // Send messenge
        $request->session()->flash('alert-success', trans('messages.verification_plan.update.success'));

        // redirect
        return redirect()->action('Admin\PlanVerificationController@index');
    }

    public function delete(Request $request, $uid)
    {
        // init
        $plans = PlanVerification::findByUid($uid);

        // delete record
        $plans->delete();

        // retur alert
        return response()->json([
            'status' => 'success',
            'message' => trans('messages.verification_plan.delete.success'),
        ]);
    }

    public function billingCycleCustom(Request $request)
    {
        // init
        $plan = $request->sender_id_plan_uid ? PlanVerification::findByUid($request->sender_id_plan_uid) : PlanVerification::newDefault();
        $plan->billing_cycle = 'custom';

        if ($request->isMethod('post')) {
            // make validator
            $validator = \Validator::make($request->all(), [
                'frequency_amount' => 'required',
                'frequency_unit' => 'required',
            ]);

            // fill
            $plan->fill($request->all());

            // redirect if fails
            if ($validator->fails()) {
                return response()->view('admin.plan_verifications.billingCycleCustom', [
                    'plan' => $plan,
                    'errors' => $validator->errors(),
                ], 400);
            }

            return view('admin.plan_verifications.billingCycle', [
                'plan' => $plan,
            ]);
        }

        //
        return view('admin.plan_verifications.billingCycleCustom', [
            'plan' => $plan,
        ]);
    }

    /**
     * Show item.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function visibleOn(Request $request, $uid)
    {
        $plan = PlanVerification::findByUid($uid);

        //
        $plan->visibleOn();

        // Redirect to my lists page
        return response()->json([
            'status' => 'success',
            'message' => trans('messages.plan.showed'),
        ], 201);
    }

    /**
     * Show item.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function visibleOff(Request $request, $uid)
    {
        $plan = PlanVerification::findByUid($uid);

        //
        $plan->visibleOff();

        // Redirect to my lists page
        return response()->json([
            'status' => 'success',
            'message' => trans('messages.plan.hidden'),
        ], 201);
    }
}
