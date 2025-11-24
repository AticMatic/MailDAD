<h4>{{ trans('messages.payment_gateway.general') }}</h4>
<div class="mb-4">
    <div class="mb-3">
        <label class="form-label">{{ trans('messages.payment_gateway.name') }}</label>
        <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
            name="name"
            value="{{ $paymentGateway->name }}"
        >
    </div>

    <div class="mb-3">
        <label class="form-label">{{ trans('messages.payment_gateway.description') }}</label>
        <input type="text" class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}"
            name="description"
            value="{{ $paymentGateway->description }}"
        >
    </div>
</div>



<h4>{{ trans('messages.payment_gateway.service_configuration') }}</h4>
@include('admin.payment_gateways.form.' . $paymentGateway->type, [
    'paymentGateway' => $paymentGateway,
])