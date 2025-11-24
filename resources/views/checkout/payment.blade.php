@extends('layouts.core.frontend', [
	'menu' => '',
])

@section('title', trans('messages.balance.your_balance'))

@section('head')
    <script type="text/javascript" src="{{ URL::asset('core/js/group-manager.js') }}"></script>
@endsection

@section('page_header')

    <div class="page-title">
        <ul class="breadcrumb breadcrumb-caret position-right">
            <li class="breadcrumb-item"><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
            <li class="breadcrumb-item active">{{ trans('messages.invoice.checkout') }}</li>
        </ul>
        <h1>
            <span class="text-semibold">
                <span class="material-symbols-rounded">payments</span>
                {{ trans('messages.checkout.payment') }}
            </span>
        </h1>
    </div>

@endsection

@section('content')
    @include('checkout._steps', [
        'step' => 'payment',
    ])

    <div class="row">
        <div class="col-md-8">
            <form id="PaymentForm" class="billing-address-form" action="{{ action('CheckoutController@checkout', [
                'invoice_uid' => $invoice->uid,
            ]) }}"
                method="POST"
            >
                {{ csrf_field() }}

                <div class="mt-5">
                    <div class="border p-4 rounded shadow-sm bg-white">
                        <div class="">
                            <div class="d-flex align-items-center mb-3">
                                <p class="me-3 mb-0">
                                    <span class="topup-header-icon">
                                        <span class="material-symbols-rounded">
                                            payments
                                        </span>
                                    </span>
                                </p>
                                <span class="display-3">
                                    {{ trans('messages.checkout.select_payment') }}
                                </span>
                            </div>
                            <hr>
                            <div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="sub-section mb-30 choose-payment-methods">
                                            @if ($invoice->total() && Auth::user()->customer->paymentMethods()->canAutoCharge()->count())

                                                <h4>{{ trans('messages.payment.use_exist_payment_method') }}</h4>
                                
                                                <p>{{ trans('messages.payment.choose_exist_payment_method') }}</p>
                                
                                                <input type="hidden" name="return_url" value="{{ action('SubscriptionController@payment', [
                                                    'invoice_uid' => $invoice->uid,
                                                ]) }}" />

                                                <div class="row mb-4">
                                                    @foreach (Auth::user()->customer->paymentMethods()->canAutoCharge()->get() as $paymentMethod)
                                                        <div class="col-md-6 mb-3">
                                                            <label class="choose-payment-method d-block h-100" for="id-{{ $paymentMethod->uid }}">
                                                                <div class="d-flex pt-3 pb-3 pl-2">
                                                                    <div class="text-end pe-2">
                                                                        <div class="d-flex align-items-center form-group-mb-0 pt-1" style="width: 30px">
                                                                            <div class="me-2">
                                                                                <input payment-control="checker" type="radio" name="payment_method_id" value="{{ $paymentMethod->uid }}" id="id-{{ $paymentMethod->uid }}" class="styled" />
                                                                                <label class="check-symbol" for="id-{{ $paymentMethod->uid }}"></label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mr-auto pr-4">
                                                                        <h5 class="font-weight-semibold mb-1">{{ $paymentMethod->getMethodTitle() }}</h5>
                                                                        <p class="mb-1">{{ $paymentMethod->getMethodInfo() }}</p>
                                                                        <p class="mb-0">
                                                                            {{ $paymentMethod->paymentGateway->name }}
                                                                        </p>
                                                                    </div>    
                                                                </div>           
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                            
                                            <h4>{{ trans('messages.payment.use_new_payment_method') }}</h4>
            
                                            <p>{{ trans('messages.payment.choose_new_payment_method_to_proceed') }}</p>

                                            @foreach (Acelle\Model\PaymentGateway::active()->get() as $paymentGateway)
                                                <label class="choose-payment-gateway d-block mb-3" for="id-{{ $paymentGateway->uid }}">
                                                    <div class="d-flex pt-3 pb-3 pl-2 choose-payment choose-payment-{{ $paymentGateway->type }}">
                                                        <div class="text-end pe-2">
                                                            <div class="d-flex align-items-center form-group-mb-0 pt-1" style="width: 30px">
                                                                <div class="me-2">
                                                                    <input payment-control="checker" type="radio" name="payment_gateway_id" value="{{ $paymentGateway->uid }}" id="id-{{ $paymentGateway->uid }}" class="styled" />
                                                                    <label class="check-symbol" for="id-{{ $paymentGateway->uid }}"></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mr-auto pr-4">
                                                            <h5 class="font-weight-semibold mb-1">{{ $paymentGateway->name }}</h5>
                                                            <p class="mb-0">
                                                                {{ $paymentGateway->description }}
                                                            </p>
                                                        </div>    
                                                    </div>           
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="d-flex align-items-center">
                        <div>
                            <a href="{{ action('CheckoutController@checkout', [
                                'invoice_uid' => $invoice->uid,
                            ]) }}" class="btn btn-light">
                                <span class="material-symbols-rounded">arrow_back</span>
                                {{ trans('messages.checkout.go_back') }}
                            </a>
                        </div>
                        <div class="ms-auto">
                            <button type="submit" href="{{ action('CheckoutController@billingAddress', [
                                'invoice_uid' => $invoice->uid,
                            ]) }}" class="btn btn-primary">
                                {{ trans('messages.checkout.pay') }}
                                <span class="material-symbols-rounded">arrow_forward</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-4 pt-5">
            <div id="orderBox">

            </div>
        </div>
    </div>
        
    <script>
        var TopUpPayment = {
            orderBox: null,

            getOrderBox: function() {
                if (this.orderBox == null) {
                    this.orderBox = new Box($('#orderBox'), '{{ action('CheckoutController@orderBox', [
                        'invoice_uid' => $invoice->uid,
                    ]) }}');
                }
                return this.orderBox;
            },

            getCheckedPaymentChecker: function() {
                return $('[payment-control="checker"]:checked');
            },

            getCheckedPaymentValue: function() {
                if (!this.getCheckedPaymentChecker().length) {
                    return null;
                }

                return this.getCheckedPaymentChecker().val();
            }
        }

        $(function() {
            // payment_gateway_id data
            if (TopUpPayment.getCheckedPaymentValue()) {
                TopUpPayment.getOrderBox().data = {
                    payment_gateway_id: TopUpPayment.getCheckedPaymentValue()
                };
            }

            TopUpPayment.getOrderBox().load();
            
            // prevent submit if no payment selected
            $('#PaymentForm').on('submit', function(e) {
                if (!TopUpPayment.getCheckedPaymentValue()) {
                    e.preventDefault();

                    new Dialog('alert', {
                        message: '{{ trans('messages.subscription.no_payment_method_selected') }}',
                        title: "{{ trans('messages.notify.error') }}"
                    });
                }
            });

            var manager = new GroupManager();
            
            $('.choose-payment-gateway').each(function() {
                manager.add({
                    radio: $(this).find('[name="payment_gateway_id"]'),
                    box: $(this),
                    key: 'payment_gateway_id',
                });
            });

            $('.choose-payment-method').each(function() {
                manager.add({
                    radio: $(this).find('[name="payment_method_id"]'),
                    box: $(this),
                    key: 'payment_method_id',
                });
            });

            manager.bind(function(group, others) {
                var doCheck = function() {
                    var checked = group.radio.is(':checked');
                    
                    if (checked) {
                        others.forEach(function(other) {
                            other.box.removeClass("current");
                            other.radio.prop('checked', false);
                        });
                        group.box.addClass("current");

                        // set payment method
                        TopUpPayment.getOrderBox().data = {};
                        TopUpPayment.getOrderBox().data[group.key] = group.radio.val();  

                        TopUpPayment.getOrderBox().load();
                    } else {
                        group.box.removeClass("current");
                    }
                };

                group.radio.on('change', function() {
                    doCheck();
                });

                group.box.on('click', function() {
                    group.radio.prop('checked', true);

                    doCheck();
                });

                doCheck();
            });
        });
        
    </script>
@endsection
