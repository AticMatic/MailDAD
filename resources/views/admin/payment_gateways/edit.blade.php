@extends('layouts.core.backend', [
	'menu' => 'payment_gateway',
])

@section('title', $paymentGateway->name)

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
			<span class="text-semibold">
                <span class="material-symbols-rounded">payments</span>
                {{ $paymentGateway->name }}
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
                        <h4 class="mb-1 fw-semibold">{{ $paymentGatewayService['name'] }}</h4>
                    </div>
                    <p>{{ $paymentGatewayService['description'] }}</p>
                </div>
            </div>

            <form id="sendingserverCreate" action="{{ action('Admin\PaymentGatewayController@update', $paymentGateway->uid) }}" method="POST">
                {{ csrf_field() }}
                <input type="hidden" name="_method" value="PATCH" />

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
