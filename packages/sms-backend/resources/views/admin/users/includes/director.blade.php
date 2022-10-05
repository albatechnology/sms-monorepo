<div class="form-group">
    <label class="required" for="company_ids">{{ trans('cruds.user.fields.company') }}</label>
    <select class="form-control select2 {{ $errors->has('company_ids') ? 'is-invalid' : '' }}" name="company_ids[]"
        id="company_ids" multiple>
        @foreach ($companies as $id => $name)
            <option value="{{ $id }}" {{ in_array($id, $selectedCompanies ?? []) ? 'selected' : '' }}>
                {{ $name }}
            </option>
        @endforeach
    </select>
    @if ($errors->has('company_ids'))
        <span class="text-danger">{{ $errors->first('company_ids') }}</span>
    @endif
    <span class="help-block">{{ trans('cruds.user.fields.company_helper') }}</span>
</div>
<script>
    $('.select2').select2();
</script>
