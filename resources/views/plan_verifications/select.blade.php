@extends('layouts.popup.large')

@section('content')
    <div class="sub-section">
        <h2>{{ trans('messages.email_verification_plan.select_plan') }}</h2>
        @include('email_verification_plans._selectType', [
            'tab' => 'recurring',
        ])
                    
        <p>{{ trans('messages.email_verification_plan.recurring.intro') }}</p>

        @if (empty($plans))
            <div class="row">
                <div class="col-md-6">
                    @include('elements._notification', [
                        'level' => 'danger',
                        'message' => trans('messages.plan.no_available_plan')
                    ])
                </div>
            </div>
        @else
            <div class="new-price-box">
                <div class="row">
                    @foreach ($plans as $key => $plan)
                        <div class="col-4">
                            <div
                                class="new-price-item mb-3 d-inline-block plan-item showed
                                    {{ Auth::user()->customer->getNewOrActiveVerificationSubscription() && Auth::user()->customer->getNewOrActiveVerificationSubscription()->plan->uid == $plan->uid ? 'disabled' : '' }}
                                ">
                                <div style="height: auto">
                                    <div class="price">
                                        {!! format_price($plan->price, $plan->currency->format, true) !!}
                                        <span class="p-currency-code">{{ $plan->currency->code }}</span>
                                    </div>
                                    <p>
                                        <span class="material-symbols-rounded text-muted2 me-1">add_task</span>
                                        <span class="fw-semibold">
                                            {{ trans('messages.email_verification_credits.count', [
                                                'number' => number_with_delimiter($plan->email_verification_credits)
                                            ]) }}
                                        </span>
                                        <span> / {{ $plan->displayFrequencyTime() }}</span>
                                    </p>
                                </div>
                                <hr class="mb-2" style="width: 40px">
                                <div style="height: 40px">
                                    <label class="plan-title fs-5 fw-600 mt-0">{{ $plan->name }}</label>
                                </div>

                                <div style="min-height: 50px">
                                    <p class="mt-4">{{ $plan->description }}</p>
                                </div>

                                <span class="time-box d-block text-center small py-2 fw-600">
                                    <div class="mb-1">
                                        <span class="material-symbols-rounded text-muted2">restore</span> {{ $plan->displayFrequencyTime() }}
                                    </div>
                                    <div>
                                        <span class="fw-normal">{{ trans('messages.plan.recurring.wording', [
                                            'time' => $plan->displayFrequencyTime(),
                                        ]) }}</span>
                                    </div>
                                </span>
                                
                                @if (Auth::user()->customer->getNewOrActiveVerificationSubscription() && Auth::user()->customer->getNewOrActiveVerificationSubscription())
                                    @if (Auth::user()->customer->getNewOrActiveVerificationSubscription()->plan->uid == $plan->uid)
                                        <a
                                            href="javascript:;"
                                            class="btn btn-secondary rounded-3 d-block mt-4 shadow-sm" disabled>
                                                {{ trans('messages.plan.current_subscribed') }}
                                        </a>
                                    @else
                                        <a
                                            link-method="POST"
                                            href="{{ action("PlanVerificationController@changePlan", [
                                                'plan_uid' => $plan->uid,
                                            ]) }}"
                                            class="btn btn-primary rounded-3 d-block mt-4 shadow-sm">
                                                {{ trans('messages.subscription.change_to_this_plan') }}
                                        </a>
                                    @endif
                                @else
                                    <a
                                        link-method="POST"
                                        href="{{ action("PlanVerificationController@assignPlan", [
                                            'plan_uid' => $plan->uid,
                                        ]) }}"
                                        class="btn btn-primary rounded-3 d-block mt-4 shadow-sm">
                                            {{ trans('messages.plan.select') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        @endif
                
    </div>
@endsection