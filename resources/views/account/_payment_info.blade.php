@if (request()->user()->customer->paymentMethods()->canAutoCharge()->count())
    <div class="border rounded-3 shadow-sm">
        <div class="header px-4 py-3 border-bottom">
            <h5 class="mb-0">{{ trans('messages.payment_methods') }}</h5>
        </div>
        <div class="p-4">
            @foreach (request()->user()->customer->paymentMethods()->canAutoCharge()->get() as $key => $paymentMethod)
                <div class="d-flex w-100 {{ $loop->last ? '' : 'mb-3 pb-3 border-bottom' }}">
                    <div class="me-3">
                        @include('admin.payment_gateways._icon', [
                            'type' => $paymentMethod->paymentGateway->type,
                        ])
                    </div>
                    <div class="w-100">
                        <div class="title">
                            <h5 class="fw-semibold mb-1">{{ $paymentMethod->getMethodTitle() }}</h5>
                        </div>
                        <p class="mb-1">{{ $paymentMethod->getMethodInfo() }}</p>
                        <p class="mb-1">{{ $paymentMethod->paymentGateway->name }}</p>
                        <p class="mb-0 text-muted small">{{ trans('messages.created_at') }}:
                            {{ Auth::user()->customer->formatDateTime($paymentMethod->created_at, 'datetime_full') }}
                        </p>
                    </div>
                    <div>
                        <a link-method="POST" link-confirm="{{ trans('messages.payment_method.delete.confirm') }}" href="{{ action('PaymentMethodController@delete', [
                            'uid' => $paymentMethod->uid,
                        ]) }}" class="btn btn-icon text-danger">
                            <span class="material-icons-outlined">delete</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        
    </div>
@endif