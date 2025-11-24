<div class="form-group">
    <label class="">{{ trans('cashier::messages.paypal.environment') }}</label>
    @include('helpers.form_control.select', [
        'name' => 'gateway_data[environment]',
        'value' => $paymentGateway->getGatewayData('environment'),
        'options' => [
            ['text' => 'Sandbox', 'value' => 'sandbox'],
            ['text' => 'Production', 'value' => 'production'],
        ],
    ])
</div>
<div class="form-group">
    <label class="">{{ trans('cashier::messages.paypal.client_id') }}</label>
    @include('helpers.form_control.text', [
        'name' => 'gateway_data[client_id]',
        'value' => $paymentGateway->getGatewayData('client_id'),
    ])
</div>
<div class="form-group">
    <label class="">{{ trans('cashier::messages.paypal.secret') }}</label>
    @include('helpers.form_control.text', [
        'name' => 'gateway_data[secret]',
        'value' => $paymentGateway->getGatewayData('secret'),
    ])
</div>