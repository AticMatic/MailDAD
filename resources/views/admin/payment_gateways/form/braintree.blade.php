<div class="form-group">
    <label class="">{{ trans('cashier::messages.braintree.environment') }}</label>
    @include('helpers.form_control.select', [
        'name' => 'gateway_data[environment]',
        'value' => $paymentGateway->getGatewayData('environment'),
        'options' =>  [['text' => 'Sandbox', 'value' => 'sandbox'],['text' => 'Production', 'value' => 'production']],
        'attributes' => [
            'required' => 'required',
        ],
    ])
</div>

<div class="form-group">
    <label class="">{{ trans('cashier::messages.braintree.merchant_id') }}</label>
    @include('helpers.form_control.text', [
        'name' => 'gateway_data[merchant_id]',
        'value' => $paymentGateway->getGatewayData('merchant_id'),
        'attributes' => [
            'required' => 'required',
        ],
    ])
</div>

<div class="form-group">
    <label class="">{{ trans('cashier::messages.braintree.public_key') }}</label>
    @include('helpers.form_control.text', [
        'name' => 'gateway_data[public_key]',
        'value' => $paymentGateway->getGatewayData('public_key'),
        'attributes' => [
            'required' => 'required',
        ],
    ])
</div>

<div class="form-group">
    <label class="">{{ trans('cashier::messages.braintree.private_key') }}</label>
    @include('helpers.form_control.text', [
        'name' => 'gateway_data[private_key]',
        'value' => $paymentGateway->getGatewayData('private_key'),
        'attributes' => [
            'required' => 'required',
        ],
    ])
</div>