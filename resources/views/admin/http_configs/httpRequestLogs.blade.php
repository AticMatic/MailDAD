@extends('layouts.popup.large')

@section('content')
    <h2>{{ trans('messages.webhook.logs') }}</h2>

    <div id="HttpRequestsLogsListContainer">
        <div class="d-flex top-list-controls top-sticky-content">
            <div class="me-auto">
                <div class="filter-box">
                    <span class="filter-group">
                        <span class="title text-semibold text-muted">{{ trans('messages.sort_by') }}</span>
                        <select class="select" name="sort_order">
                            <option value="http_request_logs.created_at">{{ trans('messages.created_at') }}</option>
                            <option value="http_request_logs.updated_at">{{ trans('messages.updated_at') }}</option>
                        </select>
                        <input type="hidden" name="sort_direction" value="desc" />
<button type="button" class="btn btn-light sort-direction" data-popup="tooltip" title="{{ trans('messages.change_sort_direction') }}" role="button" class="btn btn-xs">
                            <span class="material-symbols-rounded desc">sort</span>
                        </button>
                    </span>
                </div>
            </div>
        </div>

        <div id="HttpRequestsLogsListContent">
        </div>
    </div>

    <script>
        var HttpRequestsLogs = {
            getList: function() {
                return makeList({
                    url: '{{ action('Admin\HttpConfigController@httpRequestLogsList', $httpRequest->uid) }}',
                    container: $('#HttpRequestsLogsListContainer'),
                    content: $('#HttpRequestsLogsListContent')
                });
            }
        };

        $(function() {
            HttpRequestsLogs.getList().load();
        });
    </script>
@endsection
