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
        <label class="form-label">{{ trans('messages.plan.email_verification_credits') }}</label>
        @include('helpers.form_control.number', [
            'name' => 'email_verification_credits',
            'value' => $plan->email_verification_credits,
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
    
    @include ('admin.plan_verifications.billingCycle', [
        'plan' => $plan,
    ])

</div>

<div class="mb-2">
    @include('helpers.form_control.checkbox', [
        'name' => 'has_trial',
        'value' => 'yes',
        'label' => trans('messages.plan.has_trial_period'),
        'attributes' => [
            'class' => 'numeric'
        ],
    ])
</div>
    

<div class="trial_settings">
    <label class="mb-2">{{ trans('messages.plan.trial_setting') }}</label>
    <div class="d-flex mb-4">
        <div class="me-3">
            @include('helpers.form_control.number', [
                'name' => 'trial_amount',
                'value' => $plan->trial_amount,
                'attributes' => [
                    'class' => 'numeric',
                    'min' => '0',
                ],
            ])
        </div>
        <div class="" style="width:100px">
            @include('helpers.form_control', [
                'type' => 'select',
                'name' => 'trial_unit',
                'value' => $plan->trial_unit,
                'options' => $plan->timeUnitOptions(),
                'help_class' => 'plan',
            ])
        </div>
    </div>
</div>

<script>
    $(function() {
        var manager = new GroupManager();
        manager.add({
            checkbox: $('[name="has_trial"]'),
            isChecked: function() {
                return $('[name="has_trial"]').is(':checked');
            },
            box: $('.trial_settings'),
            textbox: $('[name="general][trial_amount]"]'),
            currentValue: $('[name="general][trial_amount]"]').val()
        });

        manager.bind(function(group) {
            var check = function() {
                if (group.isChecked()) {
                    group.box.show();

                    group.textbox.prop('min', '1');
                    if (group.currentValue > 0) {
                        group.textbox.val(group.currentValue);
                    } else {
                        group.textbox.val(1);
                    }
                } else {
                    group.box.hide();
                    group.currentValue = group.textbox.val();

                    group.textbox.prop('min', '0');
                    group.textbox.val(0);
                }
            };

            group.checkbox.on('change', function() {
                check();
            });

            check();
        });
    })
</script>