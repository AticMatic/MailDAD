<div class="form-group">
    <label class="">{{ trans('cashier::messages.paystack.public_key') }}</label>
    @include('helpers.form_control.text', [
        'name' => 'gateway_data[public_key]',
        'value' => $paymentGateway->getGatewayData('public_key'),
    ])
</div>
<div class="form-group">
    <label class="">{{ trans('cashier::messages.paystack.secret_key') }}</label>
    @include('helpers.form_control.text', [
        'name' => 'gateway_data[secret_key]',
        'value' => $paymentGateway->getGatewayData('secret_key'),
    ])
</div>