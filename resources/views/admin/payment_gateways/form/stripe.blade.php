<div class="form-group">
    <label class="">{{ trans('cashier::messages.stripe.publishable_key') }}</label>
    @include('helpers.form_control.text', [
        'name' => 'gateway_data[publishable_key]',
        'value' => $paymentGateway->getGatewayData('publishable_key'),
    ])
</div>
<div class="form-group">
    <label class="">{{ trans('cashier::messages.stripe.secret_key') }}</label>
    @include('helpers.form_control.text', [
        'name' => 'gateway_data[secret_key]',
        'value' => $paymentGateway->getGatewayData('secret_key'),
    ])
</div>