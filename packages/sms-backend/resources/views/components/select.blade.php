<div class="form-group">
    <label class="required" for="{{$key}}">{{ $label }}</label>
    <select class="form-control select2 {{ $errors->has($key) ? 'is-invalid' : '' }}" name="{{$key}}" required>
        @foreach($options as $option)
            <option value="{{ $option->id }}" {{ in_array($option->id, old($key,  $value ? [$value] : [])) ? 'selected' : '' }}>{{ $option->$optionLabel }}</option>
        @endforeach
    </select>
    @if($errors->has($key))
        <span class="text-danger">{{ $errors->first($key) }}</span>
    @endif
    <span class="help-block">{{ $label_helper }}</span>
</div>