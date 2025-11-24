<div id="billingCycleSelectContainer">
    <div class="mb-3">
        <label class="form-label">{{ trans('messages.time.billing_cycle') }}</label>
        <div>
            <select cycle-control="select-box" class="select w-100" name="billing_cycle">
                @foreach ($plan->billingCycleValues() as $key => $option)
                    <option {{ $plan->frequency_amount == $option['frequency_amount'] && $plan->frequency_unit == $option['frequency_unit'] ? 'selected' : '' }} value="{{ $key }}">
                        {{ trans('messages.plan.billing_cycle.' . $key) }}
                    </option>
                @endforeach

                <option value="custom">{{ trans('messages.plan.billing_cycle.custom') }}</option>

                @if ($plan->frequency_amount != 1)
                    <option selected value="other">
                        {{  trans('messages.time.billing_cycle.phrase', [
                            'frequency_amount' => number_with_delimiter($plan->getFrequencyAmount()),
                            'frequency_unit' => $plan->getFrequencyUnit(),
                        ]) }}
                    </option>
                @endif
            </select>
        </div>
    </div>

    <input type="hidden" name="frequency_amount" value="{{ $plan->getFrequencyAmount() }}" />
    <input type="hidden" name="frequency_unit" value="{{ $plan->getFrequencyUnit() }}" />
</div>

<script>
    var planBillingCycle;
    $(function() {
        // billing cycle custom
        planBillingCycle = new PlanSenderIdBillingCycle({
            container: document.querySelector('#billingCycleSelectContainer'),
            customUrl: '{{ action('Admin\PlanVerificationController@billingCycleCustom', [
                'number_plan_uid' => $plan->uid,
            ]) }}',
        });
    });

    var PlanSenderIdBillingCycle = class {
        constructor(options) {
            this.container = options.container;
            this.customUrl = options.customUrl;

            // popup
            this.popup = new Popup();

            // events
            this.events();
        }

        getSelectBox() {
            return this.container.querySelector('[cycle-control="select-box"]');
        }

        getCustomForm() {
            return this.popup.popup.find('[cycle-control="form"]');
        }

        getCycleValue() {
            return this.getSelectBox().value;
        }

        loadCustomPopup() {
            var _this = this;

            // get popup content
            $.ajax({
                url: this.customUrl,
            }).done(function(response) {
                // load popup content
                _this.popup.loadHtml(response, function() {
                    _this.afterPopupLoaded();
                });
            }).fail(function(jqXHR, textStatus, errorThrown){
            }).always(function() {
            });
        }

        events() {
            var _this = this;

            // change cycle
            $(this.getSelectBox()).on('change', (e) => {
                if (_this.getCycleValue() == 'custom') {
                    _this.loadCustomPopup();
                } else {
                    _this.updateValues();
                }
            });
        }

        updateValues() {
            var cycle = this.getCycleValue();

            switch(cycle) {
                case 'daily':
                    $('[name="frequency_amount"]').val(1);
                    $('[name="frequency_unit"]').val('day');
                    break;
                case 'weekly':
                    $('[name="frequency_amount"]').val(1);
                    $('[name="frequency_unit"]').val('week');
                    break;
                case 'monthly':
                    $('[name="frequency_amount"]').val(1);
                    $('[name="frequency_unit"]').val('month');
                    break;
                case 'yearly':
                    $('[name="frequency_amount"]').val(1);
                    $('[name="frequency_unit"]').val('year');
                    break;
            }
        }

        afterPopupLoaded() {
            var _this = this;
            console.log(this.getCustomForm());

            this.getCustomForm().on('submit', function(e) {
                e.preventDefault();

                _this.saveCustomCycle();
            });
        }

        saveCustomCycle() {
            var _this = this;
            var data = this.getCustomForm().serialize();

            // copy
            $.ajax({
                url: this.customUrl,
                type: 'POST',
                data: data,
                globalError: false
            }).done(function(response) {
                $(_this.container).html(response);

                initJs($(_this.container));

                _this.popup.hide();
            }).fail(function(jqXHR, textStatus, errorThrown){
                // validation
                _this.popup.loadHtml(jqXHR.responseText);
            }).always(function() {
            });
        }
    }
</script>