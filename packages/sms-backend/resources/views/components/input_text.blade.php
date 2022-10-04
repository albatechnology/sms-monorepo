<div class="form-group">
    <label class="{{ $required ? 'required' : '' }}" for="{{$key}}">{{ $label }}</label>
    @if(!empty(trim($label_helper)))
        <span class="help-block"> ({{ $label_helper }})</span>
    @endif
    <input class="form-control {{ $errors->has($key) ? 'is-invalid' : '' }} {{ $input_type === 'datetime' ? 'datetime' : '' }}"
           type="text" {{ $disabled ? 'disabled' : '' }} name="{{$key}}" id="{{$key}}" value="{{ old($key, $value) }}"
            {{ $required ? 'required' : '' }}>
    @if($errors->has($key))

        <span class="text-danger">{{ $errors->first($key) }}</span>
    @endif
    <span class="help-block">{{ $label_helper }}</span>
</div>