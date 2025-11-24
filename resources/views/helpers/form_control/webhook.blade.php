

	<div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
		<label class="fw-semibold">
			{{ trans('messages.webhook.webhook_name') }}
			<span class="text-danger">*</span>
		</label>

		@include('helpers.form_control.text', [
			'name' => 'webhook[name]',
			'value' => $webhook->name,
			'attributes' => [
				'required' => true,
			]
		])
	</div>

	<div class="row">
		<div class="col-md-6">
			<div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
				<label class="fw-semibold">
					{{ trans('messages.webhook.retry_attemps') }}
					<span class="text-danger">*</span>
				</label>
				<p class="mb-2">{{ trans('messages.webhook.retry_attemps.wording') }}</p>
				@include('helpers.form_control.number', [
					'name' => 'webhook[setting_retry_times]',
					'value' => $webhook->setting_retry_times,
					'attributes' => [
						'required' => true,
					]
				])
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
				<label class="fw-semibold">
					{{ trans('messages.webhook.retry_after_seconds') }}
					<span class="text-danger">*</span>
				</label>
				<p class="mb-2">{{ trans('messages.webhook.retry_after_seconds.wording') }}</p>
				@include('helpers.form_control.number', [
					'name' => 'webhook[setting_retry_after_seconds]',
					'value' => $webhook->setting_retry_after_seconds,
					'attributes' => [
						'required' => true,
					]
				])
			</div>
		</div>
	</div>

	@include('helpers.form_control.httpConfig', [
		'httpConfig' => $webhook->getHttpConfig(),
		'formId' => $formId,
		'testUrl' => $testUrl,
		'tags' => $tags,
	])