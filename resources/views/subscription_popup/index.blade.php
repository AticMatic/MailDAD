@extends('layouts.popup.large')

@section('content')
    <div data-control="subscription-popup" class="row">
        <div class="col-sm-12 col-md-8 col-lg-8">
            @if ($subscription->isActive())
                <div class="notification_group">
                    @if ($subscription->getItsOnlyUnpaidRenewInvoice())
                        @if (!\Auth::user()->customer->preferredPaymentGatewayCanAutoCharge())
                            @include('elements._notification', [
                                'level' => 'warning',
                                'message' => trans('messages.have_new_renew_invoice')
                            ])
                        @else
                            @include('elements._notification', [
                                'level' => 'warning',
                                'message' => trans('messages.have_new_renew_invoice.auto', [
                                    'date' => Auth::user()->customer->formatDateTime($subscription->getDueDate(), 'datetime_full'),
                                ])
                            ])

                            @if ($subscription->getItsOnlyUnpaidRenewInvoice()->lastTransactionIsFailed())
                                @include('elements._notification', [
                                    'level' => 'danger',
                                    'message' => $subscription->getItsOnlyUnpaidRenewInvoice()->lastTransaction()->error
                                ])
                            @endif
                        @endif
                    @endif

                    @if ($subscription->getItsOnlyUnpaidChangePlanInvoice())
                        @include('elements._notification', [
                            'level' => 'warning',
                            'message' => trans('messages.have_new_change_plan_invoice', [
                                'link' => action('SubscriptionController@payment', [
                                    'invoice_uid' => $subscription->getItsOnlyUnpaidChangePlanInvoice()->uid,
                                ]),
                            ])
                        ])
                    @endif
                </div>
            @endif

            <h2 class="text-semibold">{{ trans('messages.subscription') }}</h2>

            <div class="sub-section">
                @if ($subscription->isActive())
                    @if ($subscription->isRecurring())
                        <p>
                            {!! trans('messages.subscription.current_subscription.wording', [
                                'plan' => $subscription->plan->name,
                                'money' => Acelle\Library\Tool::format_price($subscription->plan->getPrice(), $subscription->plan->currency->format),
                                'remain' => $subscription->current_period_ends_at->diffForHumans(),
                                'next_on' => Auth::user()->customer->formatDateTime($subscription->current_period_ends_at, 'datetime_full')
                            ]) !!}
                        </p>
                    @else
                        <p>
                            {!! trans('messages.subscription.current_subscription.cancel_at_end_of_period.wording', [
                                'plan' => $subscription->plan->name,
                                'money' => Acelle\Library\Tool::format_price($subscription->plan->getPrice(), $subscription->plan->currency->format),
                                'remain' => $subscription->current_period_ends_at->diffForHumans(),
                                'end_at' => Auth::user()->customer->formatDateTime($subscription->current_period_ends_at, 'datetime_full')
                            ]) !!}
                        </p>
                    @endif                        

                    @if (\Auth::user()->customer->can('disableRecurring', $subscription))
                        <a data-action="disable-recurring" data-confirm="{{ trans('messages.subscription.disable_recurring.confirm') }}"
                            href="{{ action('SubscriptionPopupController@disableRecurring', [
                                'uid' => $subscription->uid,
                            ]) }}"
                            class="btn btn-secondary me-1"
                        >
                            {{ trans('messages.subscription.disable_recurring') }}
                        </a>
                    @endif

                    @if (\Auth::user()->customer->can('enableRecurring', $subscription))
                        <a data-action="enable-recurring" data-confirm="{{ trans('messages.subscription.enable_recurring.confirm') }}"
                            href="{{ action('SubscriptionPopupController@enableRecurring', [
                                'uid' => $subscription->uid,
                            ]) }}"
                            class="btn btn-secondary me-2"
                        >
                            {{ trans('messages.subscription.enable_recurring') }}
                        </a>
                    @endif

                    @if (\Auth::user()->customer->can('cancelNow', $subscription))
                        <a data-action="cancel-now" data-confirm="{{ trans('messages.subscription.cancel_now.confirm') }}"
                            href="{{ action('SubscriptionPopupController@cancelNow', [
                                'uid' => $subscription->uid,
                            ]) }}"
                            class="btn btn-danger me-2"
                        >
                            {{ trans('messages.subscription.cancel_now') }}
                        </a>
                    @endif
                @endif
            </div>
            @include('subscription._invoices')
        </div>
        <div class="col-sm-12 col-md-4 col-lg-4">
            @if ($subscription->isActive())
                @if ($subscription->getItsOnlyUnpaidChangePlanInvoice())
                    <div class="card shadow-sm rounded-3 px-2 py-2 mb-4">
                        
                        <div class="card-body p-4">
                            @include('invoices.bill', [
                                'bill' => $subscription->getItsOnlyUnpaidChangePlanInvoice()->getBillingInfo(),
                            ])

                            <hr>
                            <div class="text-left">
                                @if ($subscription->getItsOnlyUnpaidChangePlanInvoice()->getLastAndItIsPendingTransaction())
                                    <div class="text-right pe-none">
                                        <a href="{{ action('SubscriptionController@payment', [
                                            'invoice_uid' => $subscription->getItsOnlyUnpaidChangePlanInvoice()->uid,
                                        ]) }}" class="btn btn-warning button-loading full-width pr-20 pe-none " style="width:100%;pointer-events: auto;opacity:0.9">
                                            {{ trans('messages.invoice.payment_is_being_verified') }}
                                            <div class="loader"></div>
                                        </a>
                                    </div>
                                @else
                                    <a href="{{ action('SubscriptionController@payment', [
                                        'invoice_uid' => $subscription->getItsOnlyUnpaidChangePlanInvoice()->uid,
                                    ]) }}" class="btn btn-secondary">
                                        {{ trans('messages.invoice.pay_now') }}
                                    </a>

                                    <a class="btn btn-link" link-method="POST" link-confirm="{{ trans('messages.invoice.cancel.confirm') }}"
                                        href="{{ action('SubscriptionController@cancelInvoice', [
                                            'invoice_uid' => $subscription->getItsOnlyUnpaidChangePlanInvoice()->uid,
                                        ]) }}">
                                        {{ trans('messages.invoice.cancel') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if ($subscription->getItsOnlyUnpaidRenewInvoice())
                    <div class="card shadow-sm rounded-3 px-2 py-2">
                        <div class="card-body p-4">
                            @include('invoices.bill', [
                                'bill' => $subscription->getItsOnlyUnpaidRenewInvoice()->getBillingInfo(),
                            ])

                            @if (\Auth::user()->customer->preferredPaymentGatewayCanAutoCharge())
                                <hr>
                                <div class="text-right pe-none">
                                    <a href="{{ action('SubscriptionController@payment', [
                                        'invoice_uid' => $subscription->getItsOnlyUnpaidRenewInvoice()->uid,
                                    ]) }}" class="btn btn-warning button-loading full-width pr-20 pe-none " style="width:100%;pointer-events: auto;opacity:0.9">
                                        {!! trans('messages.invoice.auto_pay_before', [
                                            'date' => Auth::user()->customer->formatDateTime($subscription->current_period_ends_at, 'date_full')
                                        ]) !!}
                                        <div class="loader"></div>
                                    </a>
                                </div>
                            @else
                                @if (!$subscription->getItsOnlyUnpaidRenewInvoice()->getLastAndItIsPendingTransaction())
                                    <hr>
                                    <div class="text-left">
                                        <a href="{{ action('SubscriptionController@payment', [
                                            'invoice_uid' => $subscription->getItsOnlyUnpaidRenewInvoice()->uid,
                                        ]) }}" class="btn btn-secondary">
                                            {{ trans('messages.invoice.pay_now') }}
                                        </a>
                                    </div>
                                @else
                                    <hr>
                                    <div class="text-right pe-none">
                                        <a href="{{ action('SubscriptionController@payment', [
                                            'invoice_uid' => $subscription->getItsOnlyUnpaidRenewInvoice()->uid,
                                        ]) }}" class="btn btn-warning button-loading full-width pr-20 pe-none " style="width:100%;pointer-events: auto;opacity:0.9">
                                            {{ trans('messages.invoice.payment_is_being_verified') }}
                                            <div class="loader"></div>
                                        </a>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                @endif
            @endif

            <div class="mt-4">
                @include('account._payment_info', [
                    'redirect' => action('SubscriptionController@index'),
                ])
            </div>
        </div>
    </div>

    <script>
        $(function() {
            var popup = new SubscriptionPopupIndex();
        });

        var SubscriptionPopupIndex = class {
            constructor() {
                this.events();
            }

            getChangePlanButton() {
                return $('[data-action="change-plan"]');
            }

            getDisableRecurringButton() {
                return $('[data-action="disable-recurring"]');
            }

            getEnableRecurringButton() {
                return $('[data-action="enable-recurring"]');
            }

            getCancelNowButton() {
                return $('[data-action="cancel-now"]');
            }

            events() {
                var _this = this;

                // change plan popup
                this.getChangePlanButton().on('click', function(e) {
                    e.preventDefault();

                    _this.showChangePlanPopup();
                });

                // disable recurring
                this.getDisableRecurringButton().on('click', function(e) {
                    e.preventDefault();

                    _this.disableRecurring();
                });

                // enabled recurring
                this.getEnableRecurringButton().on('click', function(e) {
                    e.preventDefault();

                    _this.enableRecurring();
                });

                // cancel now subscription
                this.getCancelNowButton().on('click', function(e) {
                    e.preventDefault();

                    _this.cancelNow();
                });
            }

            showChangePlanPopup(url) {
                let popup = new Popup();
                popup.load(this.getChangePlanButton().attr('href'));
            }

            disableRecurring() {
                var url = this.getDisableRecurringButton().attr('href');
                var confirm = this.getDisableRecurringButton().attr('data-confirm');

                new Dialog('confirm', {
                    message: confirm,
                    ok: function() {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _token: CSRF_TOKEN,
                            }
                        }).done(function(response){
                            SubscriptionPopup.refresh();

                            //
                            notify({
                                type: response.status,
                                message: response.message,
                            });
                        }).fail(function(jqXHR, textStatus, errorThrown){
                        }).always(function() {
                        });
                    }
                });
            }

            enableRecurring() {
                var url = this.getEnableRecurringButton().attr('href');
                var confirm = this.getEnableRecurringButton().attr('data-confirm');

                new Dialog('confirm', {
                    message: confirm,
                    ok: function() {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _token: CSRF_TOKEN,
                            }
                        }).done(function(response){
                            SubscriptionPopup.refresh();

                            //
                            notify({
                                type: response.status,
                                message: response.message,
                            });
                        }).fail(function(jqXHR, textStatus, errorThrown){
                        }).always(function() {
                        });
                    }
                });
            }

            cancelNow() {
                var url = this.getCancelNowButton().attr('href');
                var confirm = this.getCancelNowButton().attr('data-confirm');

                new Dialog('confirm', {
                    message: confirm,
                    ok: function() {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _token: CSRF_TOKEN,
                            }
                        }).done(function(response){
                            SubscriptionPopup.close();

                            window.location.reload();

                            //
                            notify({
                                type: response.status,
                                message: response.message,
                            });
                        }).fail(function(jqXHR, textStatus, errorThrown){
                        }).always(function() {
                            SubscriptionPopup.close();
                        });
                    }
                });
            }
        }
    </script>
@endsection

