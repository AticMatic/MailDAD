<h4>{{ trans('messages.payment_gateway.delete.confirm') }}</h4>
<ul class="modern-listing">
    @foreach ($paymentGateways->get() as $paymentGateway)
        <li class="d-flex align-items-center">
            <i class="material-symbols-rounded fs-4 me-3 text-danger">error_outline</i>
            <div>
                <h5 class="text-danger mb-1">{{ $paymentGateway->name }}</h5>
            </div>                      
        </li>
    @endforeach
</ul>