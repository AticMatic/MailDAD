<div class="input-icon-right">											
    <input {{ isset($disabled) && $disabled == true ? ' disabled="disabled"' : "" }} id="{{ $name }}" placeholder="{{ isset($placeholder) ? $placeholder : "" }}" value="{{ isset($value) ? $value : "" }}" type="text" name="{{ $name }}" class="form-control pickadatetime"
    
    @if (isset($attributes))
        @foreach ($attributes as $k => $v)
            @if (!in_array($k, ['class']))
                {{ $k }}="{{ $v }}"
            @endif
        @endforeach
    @endif
    >
    <span class="date-input-icon">🕛</span>
</div>