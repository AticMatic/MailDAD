<h4 class="mb-3">
    {{ trans('messages.automation.action.wait_until') }}
</h4>
<p class="mb-3">
    {{ trans('messages.automation.action.wait_until.intro') }}
</p>

<div class="row">
    <div class="col-md-6 wait-time">    
        <div class="form-group">
            <label for="" class="form-label">{{ trans('messages.date')  }}</label>
            @include('helpers.form_control.date', [
                'name' => 'date',
                'value' => $element->getOption('wait_until') ? Carbon\Carbon::parse($element->getOption('wait_until'))->format('Y-m-d') : '',
                'attributes' => [
                    'required' => true,
                ]
            ])
        </div>
    </div>
    <div class="col-md-6 wait-time"> 
        <div class="form-group">   
            <label for="" class="form-label">{{ trans('messages.automation.at')  }}</label>
            @include('helpers.form_control.time', [
                'name' => 'time',
                'value' => $element->getOption('wait_until') ? Carbon\Carbon::parse($element->getOption('wait_until'))->format('H:i') : '',
                'attributes' => [
                    'required' => true,
                ]
            ])
        </div>
    </div>
</div>

<script>
    var waitTimePopup = new Popup();

    $('.wait-time [name=time]').change(function() {
        var val = $(this).val();

        if (val == 'custom') {
            waitTimePopup.load('{{ action('Automation2Controller@waitTime', $automation->uid) }}');
        }
    });
</script>