<div class="form-group">
    <label class="required" for="company_id">{{ trans('cruds.user.fields.company') }}</label>
    <select class="form-control select2 {{ $errors->has('company_id') ? 'is-invalid' : '' }}" name="company_id"
        id="company_id">
        <option value="">{{ trans('global.pleaseSelect') }}</option>
        @foreach ($companies as $id => $name)
            <option value="{{ $id }}">
                {{ $name }}
            </option>
        @endforeach
    </select>
    @if ($errors->has('company_id'))
        <span class="text-danger">{{ $errors->first('company_id') }}</span>
    @endif
    <span class="help-block">{{ trans('cruds.user.fields.company_helper') }}</span>
</div>
<div class="form-group">
    <label for="supervisor_id">{{ trans('cruds.user.fields.supervisor') }}</label>
    <select class="form-control select2 {{ $errors->has('supervisor_id') ? 'is-invalid' : '' }}" name="supervisor_id"
        id="supervisor_id" disabled>
        {{-- @foreach ($supervisors as $id => $supervisor)
            <option value="{{ $id }}" {{ old('supervisor_id') == $id ? 'selected' : '' }}>
                {{ $supervisor }}</option>
        @endforeach --}}
    </select>
    @if ($errors->has('supervisor_id'))
        <span class="text-danger">{{ $errors->first('supervisor_id') }}</span>
    @endif
    <span class="help-block">{{ trans('cruds.user.fields.supervisor_helper') }}</span>
</div>
<div class="form-group">
    <label class="required" for="channel_id">{{ trans('cruds.user.fields.channels') }}</label>
    {{-- <div style="padding-bottom: 4px">
        <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
        <span class="btn btn-info btn-xs deselect-all"
            style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
    </div> --}}
    <select class="form-control select2 {{ $errors->has('channel_id') ? 'is-invalid' : '' }}" name="channel_id"
        id="channel_id" disabled data-placeholder="Select channels">
    </select>
    @if ($errors->has('channel_id'))
        <span class="text-danger">{{ $errors->first('channel_id') }}</span>
    @endif
    <span class="help-block">{{ trans('cruds.user.fields.channels_helper') }}</span>
</div>
<div class="form-group">
    <label>Product Brand</label>
    {{-- <div class="mb-1">
        <button type="button" class="btn btn-success btn-xs btnSelectAll">Select All</button>
        <button type="button" class="btn btn-success btn-xs btnDeselectAll">Deselect All</button>
    </div> --}}
    <select name="product_brand_ids[]" id="product_brand_ids"
        class="form-control select2 @error('product_brand_ids') is-invalid @enderror" multiple disabled>
    </select>
    @error('product_brand_ids')
        <span class="error invalid-feedback">{{ $message }}</span>
    @enderror
</div>
<script>
    $('.select2').select2();

    $('#company_id').on('change', function() {
        var options = '';
        $('#supervisor_id').attr('disabled', true).html(options).val('').change();
        if ($(this).val()) {
            $.get("{{ url('admin/users/get-users') }}?type=3&supervisor_type_id=1&company_id=" + $(this).val(),
                function(
                    res) {
                    res.forEach(data => {
                        options += '<option value="' + data.id + '">' + data.name + '</option>';
                    });
                    $('#supervisor_id').attr('disabled', false).html(options).val('').change();
                })
        } else {
            $('#supervisor_id').attr('disabled', true).html(options).val('').change();
        }


        $('#product_brand_ids').attr('disabled', true).html(options).val('').change();
        var options = '';
        if ($(this).val().length > 0) {
            $.get('{{ url('admin/product-brands/get-product-brands') }}?company_id=' + $(this)
                .val(),
                function(
                    res) {
                    res.forEach(data => {
                        options += '<option value="' + data.id + '">' + data.name +
                            '</option>';
                    });
                    $('#product_brand_ids').attr('disabled', false).html(options).val('')
                        .change();
                })
        } else {
            $('#product_brand_ids').attr('disabled', true).html(options).val('').change();
        }
    });

    $('#supervisor_id').on('change', function() {
        var options = '';
        $('#channel_id').attr('disabled', true).html(options).val('').change();
        if ($(this).val()) {
            $.get("{{ url('admin/channels/get-channels') }}?supervisor_id=" + $(this).val(), function(
                res) {
                res.forEach(data => {
                    options += '<option value="' + data.id + '">' + data.name + '</option>';
                });
                $('#channel_id').attr('disabled', false).html(options).val('').change();
            })
        } else {
            $('#channel_id').attr('disabled', true).html(options).val('').change();
        }
    });
</script>
