<?php

namespace Acelle\Http\Controllers\Admin;

use Acelle\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Acelle\Model\PaymentGateway;
use Acelle\Library\Facades\Billing;

class PaymentGatewayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('admin.payment_gateways.index');
    }

    public function selectType(Request $request)
    {
        $paymentGatewayServices = Billing::getGateways();

        return view('admin.payment_gateways.selectType', [
            'paymentGatewayServices' => $paymentGatewayServices,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $paymentGateways = PaymentGateway::search($request->keyword)
            ->orderBy($request->sort_order, $request->sort_direction ? $request->sort_direction : 'asc')
            ->paginate($request->per_page);

        return view('admin.payment_gateways._list', [
            'paymentGateways' => $paymentGateways,
        ]);
    }

    public function create(Request $request)
    {
        // init
        $paymentGateway = PaymentGateway::newDefault($request->type);

        //
        return view('admin.payment_gateways.create', [
            'paymentGateway' => $paymentGateway,
            'paymentGatewayService' => Billing::getGateways()[$paymentGateway->type],
        ]);
    }

    public function store(Request $request)
    {
        // init
        $paymentGateway = PaymentGateway::newDefault($request->type);

        // Try to save
        $validator = $paymentGateway->savePaymentGateway(
            $request->name,
            $request->description,
            $request->gateway_data
        );

        // if error
        if ($validator->fails()) {
            return response()->view('admin.payment_gateways.create', [
                'paymentGateway' => $paymentGateway,
                'paymentGatewayService' => Billing::getGateways()[$paymentGateway->type],
                'errors' => $validator->errors(),
            ], 400);
        }

        // Send messenge
        $request->session()->flash('alert-success', trans('messages.payment_gateway.created'));

        // redirect
        return redirect()->action('Admin\PaymentGatewayController@index');
    }

    public function edit(Request $request, $uid)
    {
        // init
        $paymentGateway = PaymentGateway::findByUid($uid);

        //
        return view('admin.payment_gateways.edit', [
            'paymentGateway' => $paymentGateway,
            'paymentGatewayService' => Billing::getGateways()[$paymentGateway->type],
        ]);
    }

    public function update(Request $request, $uid)
    {
        // init
        $paymentGateway = PaymentGateway::findByUid($uid);

        // Try to save
        $validator = $paymentGateway->savePaymentGateway(
            $request->name,
            $request->description,
            $request->gateway_data
        );

        // if error
        if ($validator->fails()) {
            return response()->view('admin.payment_gateways.edit', [
                'paymentGateway' => $paymentGateway,
                'paymentGatewayService' => Billing::getGateways()[$paymentGateway->type],
                'errors' => $validator->errors(),
            ], 400);
        }

        // Send messenge
        $request->session()->flash('alert-success', trans('messages.payment_gateway.updated'));

        // redirect
        return redirect()->action('Admin\PaymentGatewayController@index');
    }

    public function delete(Request $request, $uid)
    {
        // init
        $paymentGateway = PaymentGateway::findByUid($uid);

        // delete record
        $paymentGateway->delete();

        // retur alert
        return response()->json([
            'status' => 'success',
            'message' => trans('messages.payment_gateway.deleted'),
        ]);
    }

    public function disable(Request $request, $uid)
    {
        // init
        $paymentGateway = PaymentGateway::findByUid($uid);

        // delete record
        $paymentGateway->disable();

        // retur alert
        return response()->json([
            'status' => 'success',
            'message' => trans('messages.payment_gateway.disabled'),
        ]);
    }

    public function enable(Request $request, $uid)
    {
        // init
        $paymentGateway = PaymentGateway::findByUid($uid);

        // delete record
        $paymentGateway->enable();

        // retur alert
        return response()->json([
            'status' => 'success',
            'message' => trans('messages.payment_gateway.enabled'),
        ]);
    }

    public function settings(Request $request)
    {
        // return view
        return view('admin.payment_gateways.settings');
    }
}
