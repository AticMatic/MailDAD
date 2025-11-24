<div class="form-group">
    <label class="">{{ trans('cashier::messages.offline.payment_instruction') }}</label>
    @include('helpers.form_control.textarea', [
        'name' => 'gateway_data[payment_instruction]',
        'value' => $paymentGateway->getGatewayData('payment_instruction'),
        'attributes' => [
            'class' => 'setting-editor',
        ]
    ])
</div>

