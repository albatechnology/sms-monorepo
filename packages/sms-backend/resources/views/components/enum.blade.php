<div class="form-group">
    <label {!! $required ? 'class="required"' : '' !!} for="{{$key}}">{{ $label }}</label>
    <select class="form-control {{ $errors->has($key) ? 'is-invalid' : '' }}" name="{{$key}}" id="{{$key}}">
        <option value
                disabled {{ old($key, $value ?? null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
        @foreach($enumClass::getInstances() as $enumOption)
            <option value="{{ $enumOption->value }}" {{ old($key, $enum->value ?? null) == $enumOption->value ? 'selected' : '' }}>{{ $enumOption->description }}</option>
        @endforeach
    </select>
    @if($errors->has($key))
        <span class="text-danger">{{ $errors->first($key) }}</span>
    @endif
    <span class="help-block">{{ $label_helper }}</span>
</div>