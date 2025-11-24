
    @if ($subscriptions->count() > 0)
        @foreach ($subscriptions as $key => $subscription)
            <div class="row">
                <div class="col-7"
                    current-page="{{ empty(request()->page) ? 1 : empty(request()->page) }}"
                >
                    <div class="border p-4 rounded-3 shadow-sm ">
                        <div class="d-flex align-items-center">
                            <div width="1%">
                                @switch($subscription->status)
                                    @case(Acelle\Model\Subscription::STATUS_ACTIVE)
                                        <i class="material-symbols-rounded fs-4 me-2 text-success">settings_backup_restore</i>
                                        @break
                                    @case(Acelle\Model\Subscription::STATUS_NEW)
                                        <i class="material-symbols-rounded fs-4 me-2 text-warning">add_circle_outline</i>
                                        @break
                                    @default
                                        <i class="material-symbols-rounded fs-4 me-2 text-muted">remove_circle_outline</i>
                                @endswitch
                            </div>
                            <div>
                                <h5 class="m-0 text-bold">
                                    <span class="kq_search d-block" href="#">
                                        {{ $subscription->planVerification->name }}
                                    </span>
                                </h5>
                                <div class="text-muted">{!! trans('messages.subscribed_by', [
                                    'name' => $subscription->customer->displayName(),
                                    'customer_link' => action('Admin\CustomerController@edit', $subscription->customer->uid)
                                ]) !!}</div>
                            </div>
                            <div class="text-center ms-auto">
                                @switch($subscription->status)
                                    @case(Acelle\Model\Subscription::STATUS_ACTIVE)
                                        <span style="cursor:pointer"
                                            class="view_invoices label label-flat bg-{{ $subscription->status }}"
                                        >
                                            {{ trans('messages.subscription.status.active') }}
                                        </span>
            
                                        @if ($subscription->getUnpaidInvoice() && $subscription->getUnpaidInvoice()->getLastAndItIsPendingTransaction() && $subscription->getUnpaidInvoice()->getLastAndItIsPendingTransaction()->allowManualReview())
                                            <div style="cursor:pointer"
                                                class="text-warning mini"
                                            >
                                                {{ trans('messages.subscription.status.pending_for_approval') }}
                                            </div>	
                                        @endif
            
                                        @break
                                    @case(Acelle\Model\Subscription::STATUS_NEW)
                                        @if ($subscription->getUnpaidInvoice())
                                            @if ($subscription->getUnpaidInvoice()->getLastAndItIsPendingTransaction() && $subscription->getUnpaidInvoice()->getLastAndItIsPendingTransaction()->allowManualReview())
                                                <span style="cursor:pointer"
                                                    class="view_invoices label bg-m-warning"
                                                >
                                                    {{ trans('messages.subscription.status.pending_for_approval') }}
                                                </span>	
                                            @else
                                                <span style="cursor:pointer"
                                                    class="view_invoices label bg-m-warning"
                                                >
                                                    {{ trans('messages.subscription.status.wait_for_payment') }}
                                                </span>	
                                            @endif
                                        @endif
                                        
                                        @break
                                    @default
                                        <span style="cursor:pointer"
                                            class="view_invoices label bg-{{ $subscription->status }}"
                                        >
                                            {{ trans('messages.subscription.status.' . $subscription->status) }}
                                        </span>
                                @endswitch
                            </div>
                        </div>
                        <hr>
                        @if ($subscription->isActive())
                            <div>
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
                            
                            </div>
                            <hr>
                        @endif
                        <div>
                            <h5 class="no-margin stat-num fw-semibold">
                                {!! format_price($subscription->plan->price, $subscription->plan->currency->format, true) !!}
                                / <span class="text-muted">{{ $subscription->plan->displayFrequencyTime() }}</span>
                            </h5>
                            <span class="text-muted2">{{ trans('messages.price') }}</span>
                        </div>
                        <hr>
                        <div>
                            <h5 class="no-margin stat-num">
                                +{{ number_with_delimiter($subscription->plan->email_verification_credits) }}
                                / <span class="text-muted">{{ $subscription->plan->displayFrequencyTime() }}
                            </h5>
                            <span class="text-muted2">{{ trans('messages.plan.email_verification_credits') }}</span>
                        </div>
                        <hr>
                        <div>
                            <h5 class="no-margin stat-num">
                                <span class="kq_search">{{ Auth::user()->customer->formatDateTime($subscription->created_at, 'datetime_full')}}</span>
                            </h5>
                            <span class="text-muted2">{{ trans('messages.subscribed_on') }}</span>
                        </div>
                        <hr>
                        <div width="15%">
                            @if ($subscription->isTerminated())
                                <h5 class="no-margin stat-num">
                                        <span class="kq_search">{{ Auth::user()->customer->formatDateTime($subscription->terminated_at, 'datetime_full') }}</span>
                                    </h5>
                                <span class="text-muted2">{{ trans('messages.subscription.terminated_at') }}</span>
                            @elseif ($subscription->isEnded())
                                <h5 class="no-margin stat-num">
                                        <span class="kq_search">{{ Auth::user()->customer->formatDateTime($subscription->current_period_ends_at, 'datetime_full') }}</span>
                                    </h5>
                                <span class="text-muted2">{{ trans('messages.subscription.subscription_ended_at') }}</span>
                            @elseif ($subscription->isCancelled())
                                <h5 class="no-margin stat-num">
                                        <span class="kq_search">{{ Auth::user()->customer->formatDateTime($subscription->cancelled_at, 'datetime_full') }}</span>
                                    </h5>
                                <span class="text-muted2">{{ trans('messages.subscription.cancelled_at') }}</span>
                            @elseif (!$subscription->isRecurring())
                                <h5 class="no-margin stat-num">
                                    @if ($subscription->current_period_ends_at)
                                        <span class="kq_search">{{ $subscription->current_period_ends_at->timezone(Auth::user()->customer->getTimezone())->diffForHumans() }}</span>
                                    @else
                                        <span class="kq_search">--</span>
                                    @endif
                                </h5>
                                <span class="text-muted2">{{ trans('messages.subscription.subscription_end') }}</span>
                            @elseif ($subscription->isRecurring())
                                <h5 class="no-margin stat-num">
                                    <span class="kq_search">
                                        @if ($subscription->current_period_ends_at)
                                            {{ $subscription->current_period_ends_at->timezone(Auth::user()->customer->getTimezone())->diffForHumans() }}
                                        @else
                                            --
                                        @endif									
                                    </span>
                                </h5>
                                <span class="text-muted2">{{ trans('messages.subscription.next_billing') }}</span>
                            @endif
                        </div>
                        
                        @if( $subscription->isNew() && $subscription->getItsOnlyUnpaidInitInvoice())
                            <hr>
                            <div>
                                <div colspan="6">
                                    @if ($subscription->getItsOnlyUnpaidInitInvoice()->lastTransactionIsFailed())
                                        @include('elements._notification', [
                                            'level' => 'danger',
                                            'message' => $subscription->getItsOnlyUnpaidInitInvoice()->lastTransaction()->error
                                        ])
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        <hr>
                        <div class="text-end text-nowrap d-flex">
                            <div class="me-auto">
                                @if (\Auth::user()->customer->can('disableRecurring', $subscription))
                                    <a link-method="POST" link-confirm="{{ trans('messages.subscription.disable_recurring.confirm') }}"
                                    href="{{ action('SubscriptionPopupController@disableRecurring', $subscription->uid) }}" class="btn btn-secondary list-action-single">
                                        {{ trans('messages.subscription.disable_recurring') }}
                                    </a>
                                @endif
                                @if (\Auth::user()->customer->can('enableRecurring', $subscription))
                                    <a link-method="POST" link-confirm="{{ trans('messages.subscription.enable_recurring.confirm') }}"
                                    href="{{ action('SubscriptionPopupController@enableRecurring', $subscription->uid) }}" class="btn btn-secondary list-action-single">
                                        {{ trans('messages.subscription.enable_recurring') }}
                                    </a>
                                @endif
                            </div>
                            <div>
                                @if($subscription->isActive())
                                    <a  class="btn btn-primary me-2"
                                        list-action="subscription" 
                                        href="{{ action('SubscriptionPopupController@index', [
                                            'uid' =>   $subscription->uid,
                                        ]) }}">
                                        <span class="material-symbols-rounded me-1">receipt_long</span>  
                                        <span>View details</span>
                                    </a>
                                    {{-- <a onclick="window.selectPlanPopup.popup.load('{{ action("PlanVerificationController@select") }}');" class="btn btn-light">
                                        <span class="material-symbols-rounded me-1">currency_exchange</span>  
                                        {{ trans('messages.invoice.change_plan') }}
                                    </a> --}}
                                @endif
                                @if( $subscription->isNew() && $subscription->getItsOnlyUnpaidInitInvoice())
                                    <a href="{{ action('CheckoutController@billingAddress', [
                                        'invoice_uid' => $subscription->getItsOnlyUnpaidInitInvoice()->uid,
                                    ]) }}" class="btn btn-secondary">
                                        <span class="material-symbols-rounded">shopping_cart</span>
                                        <span>Continue to The checkout</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @if ($subscription->isActive())
                    <div class="col-5">
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
                                                <a href="{{ action('CheckoutController@billingAddress', [
                                                    'invoice_uid' => $subscription->getItsOnlyUnpaidChangePlanInvoice()->uid,
                                                ]) }}" class="btn btn-warning button-loading full-width pr-20 pe-none " style="width:100%;pointer-events: auto;opacity:0.9">
                                                    {{ trans('messages.invoice.payment_is_being_verified') }}
                                                    <div class="loader"></div>
                                                </a>
                                            </div>
                                        @else
                                            <a href="{{ action('CheckoutController@billingAddress', [
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
                
                                    @if (\Auth::user()->customer->getFirstPaymentMethodThatCanAutoCharge())
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
                                        <hr>
                                        <div class="text-left mt-2 text-center">
                                            <a href="{{ action('SubscriptionController@payment', [
                                                'invoice_uid' => $subscription->getItsOnlyUnpaidRenewInvoice()->uid,
                                            ]) }}" class="">
                                                {{ trans('messages.invoice.or_you_can_manually_pay') }}
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
                    </div>
                @endif
            </div>
        @endforeach
        {{-- @include('elements/_per_page_select', ["items" => $subscriptions]) --}}
    

        <script>
            $(function() {  
                // SubscriptionPopup
                SubscriptionPopup.init();

                //
                $('[list-action="subscription"]').on('click', function(e) {
                    e.preventDefault();

                    SubscriptionPopup.load($(this).attr('href'));
                });
            });

            var SubscriptionPopup = {
                init: function() {
                    this.popup = new Popup();
                },

                load: function(url) {
                    this.popup.load(url);
                },

                refresh: function() {
                    this.load();
                },

                close: function() {
                    // hide popup
                    this.popup.hide();
                }
            }
        </script>
    @else
        <div class="row">
            <div class="col-6">
                <div class="empty-list">
                    <span class="material-symbols-rounded">assignment_turned_in</span>
                    <span class="line-1">
                        {{ trans('messages.plan.you_have_not_subscribed_yet') }}
                    </span>
                    <div class="mt-2">
                        <a onclick="window.selectPlanPopup.popup.load('{{ action("PlanVerificationController@select") }}');" class="btn btn-primary">
                            {{ trans('messages.plan.subscribe_to_a_plan') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
