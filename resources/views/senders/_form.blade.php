<div class="row">
	<div class="col-md-6">
		<p>{{ trans('messages.sender.new.wording') }}</p>
			
		<div class="sub_section">
			@include('helpers.form_control', [
				'type' => 'text',
				'name' => 'name',
				'value' => $sender->name,
				'help_class' => 'sender',
				'rules' => $sender->rules()
			])

			@include('helpers.form_control', [
				'type' => 'text',
				'disabled' => isset($sender->id),
				'name' => 'email',
				'value' => $sender->email,
				'help_class' => 'sender',
				'rules' => $sender->rules()
			])

            @if (!isset($sender->id))
                <div class="mailbox-creation-group">
                    <hr>
                    <p class="small text-muted mb-2">Optional: Create mailbox on Mailcow server</p>
                    @include('helpers.form_control', [
                        'type' => 'password',
                        'name' => 'mailbox_password',
                        'label' => 'Mailbox Password',
                        'value' => '',
                        'help_class' => 'sender',
                        'rules' => [],
                        'eye' => true,
                        'help' => 'Leave empty to only verify an existing email address. Enter a password to create this mailbox on the server.'
                    ])
                </div>
            @endif

		</div>
		<div class="text-left">
			<button class="btn btn-secondary me-1"><i class="icon-check"></i> {{ trans('messages.save') }}</button>
			<a href="{{ action("SenderController@index") }}" class="btn btn-secondary"><span class="material-symbols-rounded">close</span> {{ trans('messages.cancel') }}</a>
		</div>
	</div>
</div>
