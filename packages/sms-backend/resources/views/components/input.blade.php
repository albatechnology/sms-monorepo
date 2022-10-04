<div class="form-group">
    <label class="required" for="stock">{{ $label }}</label>
    <span class="help-block">{{ $label_helper }}</span>
    <input class="form-control {{ $errors->has($key) ? 'is-invalid' : '' }}"
           type="number"
           name="{{$key}}"
           id="{{$key}}"
           value="{{$value}}"

           @if($type == \App\View\Components\Input::TYPE_NUMBER)
           step="1"
           @endif

           required
    >
    @if($errors->has($key))
        <span class="text-danger">{{ $errors->first($key) }}</span>
    @endif
</div>
