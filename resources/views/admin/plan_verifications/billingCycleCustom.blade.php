@extends('layouts.popup.small')

@section('content')
    <form cycle-control="form" action="{{ action('Admin\PlanVerificationController@billingCycleCustom', [
        'number_plan_uid' => $plan->uid,
    ]) }}" method="POST">
        {{ csrf_field() }}
        
        <h2 class="text-semibold">{{ trans('messages.time.billing_cycle') }}</h2>

        <div class="mb-3">
            <label class="form-label">{{ trans('messages.time.frequency_amount') }}</label>
            <input type="number" class="form-control {{ $errors->has('frequency_amount') ? 'is-invalid' : '' }}"
                name="frequency_amount"
                value="{{ $plan->getFrequencyAmount() }}"
            >
            @if ($errors->has('frequency_amount'))
                <div class="invalid-feedback">
                    {{ $errors->first('frequency_amount') }}
                </div>
            @endif
        </div>

        <div class="mb-3">
            <label class="form-label">{{ trans('messages.time.frequency_unit') }}</label>
            <select name="frequency_unit" class="form-select">
                @foreach ($plan->timeUnitOptions() as $option)
                    <option {{ $plan->getFrequencyUnit() == $option['value'] ? 'selected' : '' }} value="{{ $option['value'] }}">{{ $option['text'] }}</option>
                @endforeach
            </select>
        </div>
        <hr>
        <button type="submit" class="btn btn-secondary me-1">{{ trans('messages.save') }}</button>
        <a href="javascript:;" class="btn btn-link close">{{ trans('messages.close') }}</a>
    </form>
@endsection

                    