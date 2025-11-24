@extends('layouts.core.backend', [
	'menu' => 'payment_gateway',
])

@section('title', trans('messages.payment_gateway.add_new'))

@section('head')
	<script type="text/javascript" src="{{ URL::asset('core/tinymce/tinymce.min.js') }}"></script>        
    <script type="text/javascript" src="{{ URL::asset('core/js/editor.js') }}"></script>
@endsection

@section('page_header')
    
	<div class="page-title">
		<ul class="breadcrumb breadcrumb-caret position-right">
			<li class="breadcrumb-item"><a href="{{ action("Admin\HomeController@index") }}">{{ trans('messages.home') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ action("Admin\PaymentGatewayController@index") }}">{{ trans('messages.payment_gateways') }}</a></li>
		</ul>
		<h1>
			<span class="text-semibold"><span class="material-symbols-rounded">badge</span>
                {{ trans('messages.payment_gateway.add_new') }}
            </span>
		</h1> 
	</div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-7">
            <div class="d-flex align-items-center border-bottom mb-4 pb-2">
                <div class="me-3">
                    @include('admin.payment_gateways._icon', [
                        'type' => $paymentGateway->type,
                    ])
                </div>
                <div class="">
                    <div class="title fw-semibold">
                        <label>{{ $paymentGatewayService['name'] }}</label>
                    </div>
                    <p>{{ $paymentGatewayService['description'] }}</p>
                </div>
                <div class="ms-auto">
                    <a class="btn btn-secondary ml-5 text-nowrap"
                        href="{{ action('Admin\PaymentGatewayController@selectType') }}">
                        {{ trans('messages.payment_gateway.type.change') }}
                    </a>
                </div>
            </div>

            <form id="sendingserverCreate" action="{{ action('Admin\PaymentGatewayController@store', [
                'type' => $paymentGateway->type,
            ]) }}" method="POST">
                {{ csrf_field() }}

                @include('admin.payment_gateways.form')

                <div>
                    <button type="submit" class="btn btn-primary me-1">
                        {{ trans('messages.save') }}
                    </button>
                    <a href="{{ action('Admin\PaymentGatewayController@index') }}" class="btn btn-light">
                        {{ trans('messages.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
