@extends('layouts.core.frontend_dark', [
    'subscriptionPage' => true,
])

@section('title', trans('messages.subscriptions'))

@section('head')
    <script type="text/javascript" src="{{ AppUrl::asset('core/js/group-manager.js') }}"></script>
@endsection

@section('menu_title')
    @include('subscription._title')
@endsection

@section('menu_right')
    @if ($invoice->type !== \Acelle\Model\InvoiceNewSubscription::TYPE_NEW_SUBSCRIPTION)
        <li class="nav-item d-flex align-items-center">
            <a  href="{{ action('SubscriptionController@index') }}"
                class="nav-link py-3 lvl-1">
                <i class="material-symbols-rounded me-2">arrow_back</i>
                <span>{{ trans('messages.go_back') }}</span>
            </a>
        </li>
    @endif

    @include('layouts.core._top_activity_log')
    @include('layouts.core._menu_frontend_user', [
        'menu' => 'subscription',
    ])
@endsection

@section('content')

    <div class="container mt-4 mb-5">
        <div class="row">
            <div class="col-md-8">
                @if (Auth::user()->customer->getNewOrActiveGeneralSubscription() && Auth::user()->customer->getNewOrActiveGeneralSubscription()->getUnpaidInvoice()  && Auth::user()->customer->getNewOrActiveGeneralSubscription()->getUnpaidInvoice()->lastTransactionIsFailed())
                    @include('elements._notification', [
                        'level' => 'danger',
                        'message' => Auth::user()->customer->getNewOrActiveGeneralSubscription()->getUnpaidInvoice()->lastTransaction()->error
                    ])
                @endif

                @include('subscription._selectPlan')

                @include('subscription._billingInformation')

                <div class="card mt-2 subscription-step">
                    <div class="card-header py-3">
                        <div class="d-flex align-items-center">
                            <div class="me-3"><label class="subscription-step-number">3</label></div>
                            <div>
                                <h5 class="fw-600 mb-0 fs-6 text-start">
                                    {{ trans('messages.subscription.payment_method.title') }}
                                </h5>
                                <p class="m-0 text-muted">{{ trans('messages.subscription.payment_method.subtitle') }}</p>
                            </div>
                        </div>                        
                    </div>
                    <div class="card-body py-4" style="padding-left: 72px;padding-right:72px">
                        @if ($invoice->total() && Auth::user()->customer->paymentMethods()->canAutoCharge()->get()->count())
                            <form id="PaymentMethodForm"
                                action=""
                                method="POST">
                                {{ csrf_field() }}

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
                                                                <input type="radio" name="payment_method_id" value="{{ $paymentMethod->uid }}" id="id-{{ $paymentMethod->uid }}" class="styled" />
                                                                <label class="check-symbol" for="id-{{ $paymentMethod->uid }}"></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mr-auto pr-4">
                                                        <p class="mb-1 fw-semibold">{{ $paymentMethod->getMethodTitle() }}</p>
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
                            </form>
                        @endif

                        <form class="edit-payment"
                            action=""
                            method="POST">
                            {{ csrf_field() }}

                            <h4>{{ trans('messages.payment.use_new_payment_method') }}</h4>
            
                            <p>{{ trans('messages.payment.choose_new_payment_method_to_proceed') }}</p>
            
                            <input type="hidden" name="return_url" value="{{ action('SubscriptionController@payment', [
                                'invoice_uid' => $invoice->uid,
                            ]) }}" />

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="sub-section mb-30 choose-payment-gateways">
                                        @foreach (Acelle\Model\PaymentGateway::active()->get() as $paymentGateway)
                                            <div class="">
                                                <label class="choose-payment-gateway d-block mb-3 d-flex align-items-center" for="id-{{ $paymentGateway->uid }}">
                                                    <div class="w-100 d-flex pt-3 pb-3 pl-2 choose-payment choose-payment-{{ $paymentGateway->type }}">
                                                        <div class="text-end pe-2">
                                                            <div class="d-flex align-items-center form-group-mb-0 pt-1" style="width: 30px">
                                                                <div class="me-2">
                                                                    <input type="radio" name="payment_gateway_id" value="{{ $paymentGateway->uid }}" id="id-{{ $paymentGateway->uid }}" class="styled" />
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
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="order-box" style="position: sticky;top: 80px;">

                </div>
            </div>
        </div>
    </div>

    <script>
        var SubscriptionPayment = {
            orderBox: null,

            getOrderBox: function() {
                if (this.orderBox == null) {
                    this.orderBox = new Box($('.order-box'), '{{ action('SubscriptionController@orderBox', [
                        'invoice_uid' => $invoice->uid,
                    ]) }}');
                }
                return this.orderBox;
            }
        }

        $(function() {
            SubscriptionPayment.getOrderBox().load();

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
                        SubscriptionPayment.getOrderBox().data = {};
                        SubscriptionPayment.getOrderBox().data[group.key] = group.radio.val();    
                        
                        SubscriptionPayment.getOrderBox().load();
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