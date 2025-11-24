@extends('layouts.core.backend', [
    'menu' => 'payment_gateway',
])

@section('title', trans('messages.payment_gateways'))

@section('page_header')

    <div class="page-title">
        <ul class="breadcrumb breadcrumb-caret position-right">
            <li class="breadcrumb-item"><a href="{{ action("Admin\HomeController@index") }}">{{ trans('messages.home') }}</a></li>
        </ul>
        <h1>
            <span class="text-semibold"><span class="material-symbols-rounded">payments</span> {{ trans('messages.payment_gateways') }}</span>
        </h1>
    </div>

@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="sub-section">
                <h2 style="margin-bottom: 10px;margin-top: 0">{{ trans('messages.payment.all_available_gateways') }}</h2>
                <p>{!! trans('messages.payment.all_available_gateways.wording') !!}</p>
                <div class="mc-list-setting mt-5">
                    @foreach ($paymentGatewayServices as $paymentGatewayService)
                        <div class="d-flex align-items-center border-bottom py-3">
                            <div class="me-3">
                                @include('admin.payment_gateways._icon', [
                                    'type' => $paymentGatewayService['type'],
                                ])
                            </div>
                            <div class="list-setting-main pe-4">
                                <div class="title">
                                    <h5 class="fw-semibold mb-2">{{ $paymentGatewayService['name'] }}</h5>
                                </div>
                                <p>{{ $paymentGatewayService['description'] }}</p>
                            </div>
                            <div class="ms-auto">
                                <a class="btn btn-primary ml-5"
                                    href="{{ action('Admin\PaymentGatewayController@create', [
                                        'type' => $paymentGatewayService['type'],
                                    ]) }}">
                                    {{ trans('messages.payment_gateway.type.select') }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div>
                    <a href="{{ action('Admin\PaymentGatewayController@index') }}" class="btn btn-light">{{ trans('messages.return_back') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection
