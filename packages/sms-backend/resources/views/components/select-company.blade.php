<div class="form-group">
    <label class="required" for="company">{{ trans('cruds.generic.fields.company') }}</label>
    <select class="form-control select2 {{ $errors->has('company_id') ? 'is-invalid' : '' }}"
            name="company_id" id="company">
        @foreach($companies as $id => $name)
            <option value="{{ $id }}" {{ $id == old('company_id', $value) ? 'selected' : '' }}>{{ $name }}</option>
        @endforeach
    </select>
    @if($errors->has('company_id'))
        <span class="text-danger">{{ $errors->first('company_id') }}</span>
    @endif
    <span class="help-block">{{ trans('cruds.generic.fields.company_helper') }}</span>
</div>