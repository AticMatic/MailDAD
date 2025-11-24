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
        'tab' => 'gateways',
    ])

    <div class="d-flex align-items-center border-bottom mb-4 mt-2 pb-4">
        <div class="me-auto"><p class="mb-0">{{ trans('messages.payment_gateway.wording') }}</p></div>
        @can('create', Acelle\Model\PaymentGateway::class)
            <div class="text-end text-nowrap ps-4">
                <a href="{{ action('Admin\PaymentGatewayController@selectType') }}" role="button" class="btn btn-secondary">
                    <span class="material-symbols-rounded">add</span> {{ trans('messages.payment_gateway.add_new') }}
                </a>
            </div>
        @endcan
    </div>

    <div class="listing-form"
        sort-url="{{ action('Admin\PlanController@sort') }}"
        data-url="{{ action('Admin\PlanController@listing') }}"
        per-page="{{ Acelle\Model\PlanGeneral::$itemsPerPage }}"
    >
        <div class="d-flex top-list-controls top-sticky-content">
            <div class="me-auto">
                <div class="filter-box">
                    <span class="filter-group">
                        <span class="title text-semibold text-muted">{{ trans('messages.sort_by') }}</span>
                        <select class="select" name="sort_order">
                            <option value="payment_gateways.name">{{ trans('messages.name') }}</option>
                            <option value="payment_gateways.created_at">{{ trans('messages.created_at') }}</option>
                        </select>
                        <input type="hidden" name="sort_direction" value="asc" />
                                            <button class="btn btn-xs sort-direction" rel="asc" data-popup="tooltip" title="{{ trans('messages.change_sort_direction') }}" role="button" class="btn btn-xs">
                            <span class="material-symbols-rounded desc">sort</span>
                        </button>
                    </span>
                    <span class="text-nowrap">
                        <input type="text" name="keyword" class="form-control search" value="{{ request()->keyword }}" placeholder="{{ trans('messages.type_to_search') }}" />
                        <span class="material-symbols-rounded">search</span>
                    </span>
                </div>
            </div>
        </div>

        <div class="pml-table-container">
        </div>
    </div>

    <script>
        var PlanIndex = {
            getList: function() {
                return makeList({
                    url: '{{ action('Admin\PaymentGatewayController@list') }}',
                    container: $('.listing-form'),
                    content: $('.pml-table-container')
                });
            }
        };

        $(function() {
            PlanIndex.getList().load();
        });
    </script>
@endsection