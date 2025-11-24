<h4>{{ trans('messages.general') }}</h4>
<div class="mb-4">
    <div class="mb-3">
        <label class="form-label">{{ trans('messages.plan.name') }}</label>
        @include('helpers.form_control.text', [
            'name' => 'name',
            'value' => $plan->name,
        ])
    </div>

    <div class="mb-3">
        <label class="form-label">{{ trans('messages.plan.description') }}</label>
        @include('helpers.form_control.text', [
            'name' => 'description',
            'value' => $plan->description,
            'attributes' => [
                'required' => 'required',
            ],
        ])
    </div>

    <div class="mb-3">
        <label class="form-label">{{ trans('messages.email_verification_plan.credits') }}</label>
        @include('helpers.form_control.number', [
            'name' => 'credits',
            'value' => $plan->credits,
            'attributes' => [
                'required' => 'required',
            ],
        ])
    </div>
</div>

<h4>{{ trans('messages.billing') }}</h4>
<div class="">
    <div class="row">
        <div class="col-8">
            <div class="mb-3">
                <label class="form-label">{{ trans('messages.price') }}</label>
                @include('helpers.form_control.number', [
                    'name' => 'price',
                    'value' => $plan->getPrice(),
                    'attributes' => [
                        'required' => 'required',
                    ],
                ])
            </div>
        </div>
        <div class="col-4">
            <div class="mb-3">
                <label class="form-label">{{ trans('messages.currency') }}</label>
                <div>
                    @include('helpers.form_control.select', [
                        'name' => 'currency_id',
                        'value' => $plan->currency_id,
                        'options' => Acelle\Model\Currency::getSelectOptions(),
                    ])
                </div>
            </div>
        </div>
    </div>
</div>