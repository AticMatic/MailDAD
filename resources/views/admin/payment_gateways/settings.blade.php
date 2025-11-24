@extends('layouts.core.backend', [
	'menu' => 'payment_gateway',
])

@section('title', trans('messages.payment_gateways'))

@section('head')
    <script type="text/javascript" src="{{ AppUrl::asset('core/js/group-manager.js') }}"></script>
@endsection

@section('page_header')

    <div class="page-title" style="padding-bottom:0">
        <ul class="breadcrumb breadcrumb-caret position-right">
            <li class="breadcrumb-item"><a href="{{ action("Admin\HomeController@index") }}">{{ trans('messages.home') }}</a></li>
        </ul>
        <h1>
            <span class="text-semibold">{{ trans('messages.payment_gateways') }}</span>
        </h1>
    </div>

@endsection

@section('content')

    @include('admin.payment_gateways._tabs', [
        'tab' => 'settings',
    ])

    <div class="sub-section">
        <form action="{{ action('Admin\SettingController@payment') }}" method="POST" class="form-validate-jqueryz">
            {{ csrf_field() }}
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <div class="d-flex" style="width:100%">
                            <div class="me-5">
                                <label class="fw-semibold">
                                    {{ trans('messages.setting.allowed_due_subscription') }}
                                </label>
                                <p class="checkbox-description mt-1 mb-0">
                                    {{ trans('messages.setting.allowed_due_subscription.help') }}
                                </p>
                            </div>
                                
                            <div class="d-flex align-items-top">
                                @include('helpers.switch', [
                                    'name' => 'allowed_due_subscription',
                                    'option' => 'yes',
                                    'unchecked_option' => 'no',
                                    'value' => \Acelle\Model\Setting::get('allowed_due_subscription'),
                                ])
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="d-flex" style="width:100%">
                            <div class="me-5">
                                <label class="fw-semibold">
                                    {{ trans('messages.setting.not_require_card_for_trial') }}
                                </label>
                                <p class="checkbox-description mt-1 mb-0">
                                    {{ trans('messages.setting.not_require_card_for_trial.help') }}
                                </p>
                            </div>
                                
                            <div class="d-flex align-items-top">
                                @include('helpers.switch', [
                                    'name' => 'not_require_card_for_trial',
                                    'option' => 'yes',
                                    'unchecked_option' => 'no',
                                    'value' => \Acelle\Model\Setting::get('not_require_card_for_trial'),
                                ])
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group checkbox-right-switch">
                        <div class="">
                            <div style="width:100%">
                                <label class="mb-1 fw-semibold">
                                    {{ trans('messages.setting.recurring_charge_before_days.title') }}:
                                    <span class="checkbox-description mt-1">
                                        {{ trans('messages.setting.recurring_charge_before_days.desc') }}
                                    </span>
                                </label>
                            </div>
                                    
                            <div class="d-flex align-items-center">
                                <span class="text-muted me-2">{{ trans('messages.setting.recurring_charge_before_days.before_text') }}</span>
                                <input id="auto_billing_period" placeholder="" required="" value="{{ \Acelle\Model\Setting::get('subscription.auto_billing_period') }}" type="number"
                                    name="auto_billing_period" class="form-control required number numeric me-2"
                                    style="display:inline-block;width:60px;font-weight:bold"
                                >
                                <span class="text-muted">{{ trans('messages.setting.recurring_charge_before_days.after_text') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <button class="btn btn-secondary">
                {{ trans('messages.save') }}
            </a>
        </form>
    </div>
@endsection