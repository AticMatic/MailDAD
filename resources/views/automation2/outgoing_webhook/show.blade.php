@include('automation2._back')

<h4 class="mb-3">{{ trans('messages.automation.outgoing_webhook') }}</h4>
<p>{{ trans('messages.automation.action.outgoing-webhook.desc', [
        'app_name' => Acelle\Model\Setting::get('site_name'),
    ]) }}</p>

@if ($httpConfig->uid != 'none')

    <div>
        <table class="table border">
            <tbody>
                <tr>
                    <th width="50%" class="bg-light fw-normal">{{ trans('messages.webhook.request_method') }}</th>
                    <td width="50%" class="text-uppercase">
                        {{ $httpConfig->request_method }}
                    </td>
                </tr>
                <tr>
                    <th width="50%" class="bg-light fw-normal">{{ trans('messages.webhook.authorization_options') }}</th>
                    <td width="50%" class="">
                        {{ trans('messages.webhook.' . $httpConfig->request_auth_type) }}
                    </td>
                </tr>
                <tr>
                    <th width="50%" class="bg-light fw-normal">
                        {{ trans('messages.webhook.endpoint_url') }}
                    </th>
                    <td width="50%" class="">
                        {{ $httpConfig->request_url }}
                    </td>
                </tr>
                <tr>
                    <th width="50%" class="bg-light fw-normal">
                        {{ trans('messages.webhook.headers') }}
                    </th>
                    <td width="50%" class="">
                        @if (count($httpConfig->getRequestHeaders()))
                            <table class="table m-0">
                                <tbody>
                                    @foreach ($httpConfig->getRequestHeaders() as $header)
                                        <tr>
                                            <th width="50%" class="fw-normal border-0 py-1 px-2 bg-light">
                                                {{ $header['key'] ?? 'N/A' }}:
                                            </th>
                                            <td width="50%" class="text-uppercase border-0 py-1 px-2" style="word-break:break-all;">
                                                {{ $header['value'] ?? 'N/A' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            {{ trans('messages.webhook.no_headers') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th width="50%" class="bg-light fw-normal">
                        {{ trans('messages.webhook.unified_body_configuration') }}
                    </th>
                    <td width="50%" class="">
                        {{ trans('messages.webhook.' . $httpConfig->request_body_type) }}
                    </td>
                </tr>
                @if ($httpConfig->request_body_type == Acelle\Model\HttpConfig::REQUEST_BODY_TYPE_KEY_VALUE && count($httpConfig->getRequestBodyParams()))
                    <tr>
                        <th width="50%" class="bg-light fw-normal">
                            {{ trans('messages.webhook.body_parameters') }}
                        </th>
                        <td width="50%" class="">
                            @if(count($httpConfig->getRequestBodyParams()))
                                <table class="table m-0">
                                    <tbody>
                                        @foreach ($httpConfig->getRequestBodyParams() as $param)
                                            <tr>
                                                <th width="50%" class="fw-normal border-0 py-1 px-2 bg-light">
                                                    {{ $param['key'] ?? 'N/A' }}:
                                                </th>
                                                <td width="50%" class="text-uppercase border-0 py-1 px-2" style="word-break:break-all;">
                                                    {{ $param['value'] ?? 'N/A' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </td>
                    </tr>
                @endif
                @if ($httpConfig->request_body_type == Acelle\Model\HttpConfig::REQUEST_BODY_TYPE_PLAIN)
                    <tr>
                        <th width="50%" class="bg-light fw-normal">
                            {{ trans('messages.webhook.plain_text') }}
                        </th>
                        <td width="50%" class="">
                            {!! $httpConfig->request_body_plain !!}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    <div>
        <button webhook-control="save" type="button" class="btn btn-secondary me-1">
            {{ trans('messages.automation.webhook.edit') }}
        </button>

        @if ($httpConfig->uid && $httpConfig->uid != 'none')
            <a href="{{ action('HttpConfigController@httpRequests', $httpConfig->uid) }}" webhook-control="logs" type="button" class="btn btn-default">
                {{ trans('messages.automation.webhook.logs') }}
            </a>
        @endif
    </div>
@else
    <div class="alert alert-warning">
        {{ trans('messages.automation.webhook.not_setup_yet') }}
    </div>

    <div>
        <button webhook-control="save" type="button" class="btn btn-secondary me-1">
            {{ trans('messages.automation.webhook.setup') }}
        </button>
    </div>
@endif



<div class="mt-4 d-flex py-3">
    <div>
        <h4 class="mb-2">
            {{ trans('messages.automation.dangerous_zone') }}
        </h4>
        <p class="">
            {{ trans('messages.automation.action.delete.wording') }}                
        </p>
        <div class="mt-3">
            <a data-control="webhook-delete" href="javascript:;" data-confirm="{{ trans('messages.automation.action.delete.confirm') }}"
                class="btn btn-secondary">
                <span class="material-symbols-rounded">delete</span> {{ trans('messages.automation.remove_this_action') }}
            </a>
        </div>
    </div>
</div>

<script>
    $(() => {
        new WebhookManager({
            url: '{!! action('Automation2Controller@outgoingWebhookSetup', [
                'uid' => $automation->uid,
                'http_config_uid' => $httpConfig->uid,
                'id' => $element->get('id'),
            ]) !!}'
        });

        new HttpRequestLog($('[webhook-control="logs"]'));
    });

    var WebhookManager = class {
        constructor(options) {
            this.url = options.url;

            //
            this.events();
        }

        getSaveButton() {
            return $('[webhook-control="save"]');
        }

        events() {
            this.getSaveButton().on('click', () => {
                this.showEditPopup();
            });
        }

        showEditPopup() {
            automationPopup.load(this.url);
        }
    }

    var HttpRequestLog = class {
        constructor(button) {
            this.button = button;
            this.popup = new Popup();

            this.events();
        }

        events() {
            this.button.on('click', (e) => {
                e.preventDefault();
                var url = this.button.attr('href');

                this.popup.load(url);
            });
        }
    }

    $('[data-control="webhook-delete"]').on('click', function(e) {
        e.preventDefault();
        
        var confirm = $(this).attr('data-confirm');
        var dialog = new Dialog('confirm', {
            message: confirm,
            ok: function(dialog) {
                // remove current node
                tree.getSelected().detach();
                
                // save tree
                saveData(function() {
                    // notify
                    notify('success', '{{ trans('messages.notify.success') }}', '{{ trans('messages.automation.action.deteled') }}');
                    
                    // load default sidebar
                    sidebar.load('{{ action('Automation2Controller@settings', $automation->uid) }}');
                });
            },
        });
    });
</script>
