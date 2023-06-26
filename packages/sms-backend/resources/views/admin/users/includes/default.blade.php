{{-- <div class="form-group">
    <label class="required" for="company_ids">{{ trans('cruds.user.fields.company') }}</label>
    <select class="form-control select2 {{ $errors->has('company_ids') ? 'is-invalid' : '' }}" name="company_ids[]"
        id="company_ids" multiple>
        @foreach ($companies as $id => $name)
            <option value="{{ $id }}"
                {{ in_array($id, $user?->companies?->pluck('id')->all() ?? []) ? 'selected' : '' }}>
                {{ $name }}
            </option>
        @endforeach
    </select>
    @if ($errors->has('company_ids'))
        <span class="text-danger">{{ $errors->first('company_ids') }}</span>
    @endif
    <span class="help-block">{{ trans('cruds.user.fields.company_helper') }}</span>
</div> --}}
<div class="form-group">
    <label class="required" for="channel_ids">{{ trans('cruds.user.fields.channels') }}</label>
    {{-- <div style="padding-bottom: 4px">
        <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
        <span class="btn btn-info btn-xs deselect-all"
            style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
    </div> --}}
    <select class="form-control select2 {{ $errors->has('channel_ids') ? 'is-invalid' : '' }}" name="channel_ids[]"
        id="channel_ids" multiple data-placeholder="Select channels">
        {{-- @if ($selectedChannels) --}}
            @foreach ($channels as $id => $name)
                <option value="{{ $id }}" @if(in_array($id, $selectedChannels ?? [])) @endif>{{ $name }}</option>
            @endforeach
        {{-- @endif --}}
    </select>
    @if ($errors->has('channel_ids'))
        <span class="text-danger">{{ $errors->first('channel_ids') }}</span>
    @endif
    <span class="help-block">{{ trans('cruds.user.fields.channels_helper') }}</span>
</div>
<script>
    $('.select2').select2();

    // $('body').on('change', '#company_id, #company_ids', function() {
    //     $('#channels').attr('disabled', true).html('');

    //     $('#channel_ids').attr('disabled', true).html(options).val('').change();
    //     var options = '';
    //     if ($(this).val().length > 0) {
    //         $.get("{{ url('admin/channels/get-channels') }}?company_id=" + $(this).val(), function(
    //             res) {
    //             res.forEach(data => {
    //                 options += '<option value="' + data.id + '">' + data.name + '</option>';
    //             });
    //             $('#channel_ids').attr('disabled', false).html(options).val('').change();
    //         })
    //     } else {
    //         $('#channel_ids').attr('disabled', true).html(options).val('').change();
    //     }
    // });
</script>
