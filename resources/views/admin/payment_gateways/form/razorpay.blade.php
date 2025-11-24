<div class="form-group">
    <label class="">{{ trans('cashier::messages.razorpay.key_id') }}</label>
    @include('helpers.form_control.text', [
        'name' => 'gateway_data[key_id]',
        'value' => $paymentGateway->getGatewayData('key_id'),
    ])
</div>
<div class="form-group">
    <label class="">{{ trans('cashier::messages.razorpay.key_secret') }}</label>
    @include('helpers.form_control.text', [
        'name' => 'gateway_data[key_secret]',
        'value' => $paymentGateway->getGatewayData('key_secret'),
    ])
</div>