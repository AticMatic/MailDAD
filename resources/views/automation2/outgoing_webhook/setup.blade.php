@extends('layouts.popup.large')

@section('content')
    <h2 class="text-semibold">{{ trans('messages.automation.action.outgoing-webhook') }}</h2>

    <p>{!! trans('messages.automation.action.outgoing-webhook.desc', [
        'app_name' => Acelle\Model\Setting::get('site_name'),
    ]) !!}</p>
    @php

    $formId = 'WebhookForm' . uniqid();
@endphp
    
    <form id="{{ $formId }}" action="{{ action('Automation2Controller@outgoingWebhookSave', [
        'uid' => $automation->uid,
        'http_config_uid' => $httpConfig->uid,
    ]) }}" method="POST" class="">
        @csrf

        @include('helpers.form_control.httpConfig', [
            'httpConfig' => $httpConfig,
            'formId' => $formId,
            'testUrl' => action('Automation2Controller@outgoingWebhookTest', [
                'uid' => $automation->uid,
                'http_config_uid' => $httpConfig->uid,
            ]),
            'tags' => $automation->getAvailableOutgoingWebhookTags(),
        ])
        
        <div class="mt-4">
			<a href="javascript:;" class="btn btn-light me-2 close"
				data-dismiss="modal">
                <span class="material-symbols-rounded me-2">
                    backspace
                </span>
                {{ trans('messages.cancel') }}
            </a>
		</div>
    </form>

    <script>
        $(() => {
            new WebhookForm({
                form: $('#{{ $formId }}'),
                url: '{{ action('Automation2Controller@outgoingWebhookSave', [
                    'uid' => $automation->uid,
                    'http_config_uid' => $httpConfig->uid,
                ]) }}',
            });
        });

        var WebhookForm = class {
            constructor(options) {
                this.form = options.form;
                this.url = options.url;

                this.events();
            }

            getSaveButton() {
                return this.form.find('[data-control="save"]');
            }

            events() {
                // save
                this.getSaveButton().on('click', () => {
                    this.save();
                });
            }

            save() {
                if (!this.form[0].reportValidity()) {
                    return;
                }

                addMaskLoading();

                $.ajax({
                    url: this.url,
                    type: 'POST',
                    data: this.form.serialize(),
                }).done((res) => {
                    // done
                    removeMaskLoading();

                    @if ($element)
                        // merge options with reponse options
                        tree.getSelected().setOptions($.extend(tree.getSelected().getOptions(), res.options));
                        tree.getSelected().validate();
                            
                        // save tree
                        saveData(function() {
                            // hide popup
                            automationPopup.hide();
                            
                            //
                            notify({
                                type: 'success',
                                title: '{!! trans('messages.notify.success') !!}',
                                message: '{{ trans('messages.webhook.updated') }}'
                            });

                            //
                            sidebar.load();
                        });
                    @else
                        console.log(res);
                        var newE = new ElementWebhook(res);
                        MyAutomation.addToTree(newE);

                        // save tree
                        saveData(function() {
                            // hide popup
                            automationPopup.hide();
                            
                            //
                            notify({
                                type: 'success',
                                title: '{!! trans('messages.notify.success') !!}',
                                message: '{{ trans('messages.webhook.added') }}'
                            });

                            // select newly added element
                            doSelectTreeElement(newE);
                        });
                    @endif
                });
            }
        }
    </script>
@endsection
