{{-- <div class="form-group">
    <label class="required" for="company_id">{{ trans('cruds.user.fields.company') }}</label>
    <select class="form-control select2 {{ $errors->has('company_id') ? 'is-invalid' : '' }}" name="company_id"
        id="company_id">
        <option value="">{{ trans('global.pleaseSelect') }}</option>
        @foreach ($companies as $id => $name)
            <option value="{{ $id }}" {{ $user?->company_id == $id ? 'selected' : '' }}>
                {{ $name }}
            </option>
        @endforeach
    </select>
    @if ($errors->has('company_id'))
        <span class="text-danger">{{ $errors->first('company_id') }}</span>
    @endif
    <span class="help-block">{{ trans('cruds.user.fields.company_helper') }}</span>
</div> --}}
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
    <label class="required" for="channel_ids">{{ trans('cruds.user.fields.channels') }}</label>
    {{-- <div style="padding-bottom: 4px">
        <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
        <span class="btn btn-info btn-xs deselect-all"
            style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
    </div> --}}
    <select class="form-control select2 {{ $errors->has('channel_ids') ? 'is-invalid' : '' }}" name="channel_ids[]"
        id="channel_ids" multiple {{ $user ? '' : 'disabled' }} data-placeholder="Select channels">
        {{-- @if ($selectedChannels)
            @foreach ($selectedChannels as $id => $name)
                <option value="{{ $id }}" selected>{{ $name }}</option>
            @endforeach
        @endif --}}
    </select>
    @if ($errors->has('channel_ids'))
        <span class="text-danger">{{ $errors->first('channel_ids') }}</span>
    @endif
    <span class="help-block">{{ trans('cruds.user.fields.channels_helper') }}</span>
</div>
{{-- <div class="form-group">
    <label>Product Brand</label>
    <select name="product_brand_ids[]" id="product_brand_ids"
        class="form-control select2 @error('product_brand_ids') is-invalid @enderror" multiple disabled>
    </select>
    @error('product_brand_ids')
        <span class="error invalid-feedback">{{ $message }}</span>
    @enderror
</div> --}}
<script>
    var selectedProductBrands = {{ $selectedProductBrands ? json_encode($selectedProductBrands) : json_encode([]) }};
    var selectedChannels = {{ $selectedChannels ? json_encode($selectedChannels) : json_encode([]) }};
    var selectedSupervisor = '{{ $user?->supervisor_id }}';
    // var selectedCompanyId = '{{ $user?->company_id }}';

    $('.select2').select2();

    function getSupervisors() {
        var options = '';
        $('#supervisor_id').attr('disabled', true).html(options).val('').change();
        // if (companyId) {
            $.get("{{ url('admin/users/get-users') }}?type=3&supervisor_type_id=1",
                function(
                    res) {
                    res.forEach(data => {
                        var selected = selectedSupervisor == data.id ? 'selected' : '';
                        options += '<option value="' + data.id + '" ' + selected + '>' + data.name +
                            '</option>';
                    });
                    $('#supervisor_id').attr('disabled', false).html(options).change();
                })
        // } else {
        //     $('#supervisor_id').attr('disabled', true).html(options).val('').change();
        // }


    }

    // function getProductBrands() {
    //     var options = '';
    //     $('#product_brand_ids').attr('disabled', true).html(options).val('').change();
    //     // if (companyId) {
    //         $.get('{{ url('admin/product-brands/get-product-brands') }}',
    //             function(
    //                 res) {
    //                 res.forEach(data => {
    //                     var selected2 = selectedProductBrands.includes(data.id) ? 'selected' : '';
    //                     options += '<option value="' + data.id + '" ' + selected2 + '>' + data.name +
    //                         '</option>';
    //                 });
    //                 $('#product_brand_ids').attr('disabled', false).html(options).change();
    //             })
    //     // } else {
    //     //     $('#product_brand_ids').attr('disabled', true).html(options).val('').change();
    //     // }
    // }

    getSupervisors();
    // getProductBrands();
    // $('#company_id').on('change', function() {
    //     getSupervisors($(this).val());
    //     getProductBrands($(this).val());
    // });

    function getChannels(supervisorId) {
        var options = '';
        $('#channel_ids').attr('disabled', true).html(options).val('').change();
        if (supervisorId) {
            $.get("{{ url('admin/channels/get-channels') }}?supervisor_id=" + supervisorId, function(
                res) {
                res.forEach(data => {
                    var selected = selectedChannels.includes(data.id) ? 'selected' : '';
                    options += '<option value="' + data.id + '" ' + selected + '>' + data.name +
                        '</option>';
                });
                $('#channel_ids').attr('disabled', false).html(options).change();
            })
        } else {
            $('#channel_ids').attr('disabled', true).html(options).val('').change();
        }
    }

    getChannels(selectedSupervisor);
    $('#supervisor_id').on('change', function() {
        getChannels($(this).val());
    });
</script>
