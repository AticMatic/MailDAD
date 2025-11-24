@extends('layouts.popup.large')

@section('content')
    <h2>{{ trans('messages.webhook.jobs') }}</h2>

    <div id="HttpRequestsListContainer">
        <div class="d-flex top-list-controls top-sticky-content">
            <div class="me-auto">
                <div class="filter-box">
                    <span class="filter-group">
                        <span class="title text-semibold text-muted">{{ trans('messages.sort_by') }}</span>
                        <select class="select" name="sort_order">
                            <option value="http_requests.created_at">{{ trans('messages.created_at') }}</option>
                            <option value="http_requests.updated_at">{{ trans('messages.updated_at') }}</option>
                        </select>
                        <input type="hidden" name="sort_direction" value="desc" />
<button type="button" class="btn btn-light sort-direction" data-popup="tooltip" title="{{ trans('messages.change_sort_direction') }}" role="button" class="btn btn-xs">
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

        <div id="HttpRequestsListContent">
        </div>
    </div>

    <script>
        var HttpConfigsIndex = {
            getList: function() {
                return makeList({
                    url: '{{ action('HttpConfigController@httpRequestsList', $httpConfig->uid) }}',
                    container: $('#HttpRequestsListContainer'),
                    content: $('#HttpRequestsListContent')
                });
            }
        };

        $(function() {
            HttpConfigsIndex.getList().load();
        });
    </script>
@endsection
