{{-- <div class="form-group">
    <label class="required" for="company_id">{{ trans('cruds.user.fields.company') }}</label>
    <select class="form-control select2 {{ $errors->has('company_id') ? 'is-invalid' : '' }}" name="company_id"
        id="company_id">
        <option value="">{{ trans('global.pleaseSelect') }}</option>
        @foreach ($companies as $id => $name)
            <option value="{{ $id }}" {{ $id == $user?->company_id ? 'selected' : '' }}>
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
    <label class="required" for="supervisor_type_id">{{ trans('cruds.user.fields.supervisor_type') }}</label>
    <select class="form-control select2 {{ $errors->has('supervisor_type') ? 'is-invalid' : '' }}"
        name="supervisor_type_id" id="supervisor_type_id">
        @foreach ($supervisor_types as $id => $supervisor_type)
            <option value="{{ $id }}" {{ $id == $user?->supervisor_type_id ? 'selected' : '' }}>
                {{ $supervisor_type }}</option>
        @endforeach
    </select>
    @if ($errors->has('supervisor_type'))
        <span class="text-danger">{{ $errors->first('supervisor_type') }}</span>
    @endif
    <span class="help-block">{{ trans('cruds.user.fields.supervisor_type_helper') }}</span>
</div>
<div class="form-group">
    <label for="supervisor_id">{{ trans('cruds.user.fields.supervisor') }}</label>
    <select class="form-control select2 {{ $errors->has('supervisor_id') ? 'is-invalid' : '' }}" name="supervisor_id"
        id="supervisor_id" {{ $user ? '' : 'disabled' }}>
        @if ($user && !is_null($user->supervisor_id))
            <option value="{{ $user->supervisor_id }}" selected>
                {{ \App\Models\User::find($user->supervisor_id)->name }}</option>
        @endif
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
<script>
    var selectedChannels = {{ $selectedChannels ? json_encode($selectedChannels) : json_encode([]) }};
    var maxSupervisorTypeId = {{ $maxSupervisorTypeId }};
    var defaultSupervisorTypeId = '{{ $user?->supervisor_type_id }}';
    var defaultSupervisorId = '{{ $user?->supervisor_id }}';
    console.log('selectedChannels', selectedChannels)
    console.log('maxSupervisorTypeId', maxSupervisorTypeId)
    console.log('defaultSupervisorTypeId', defaultSupervisorTypeId)
    console.log('defaultSupervisorId', defaultSupervisorId)
    $('.select2').select2();

    function getSupervisors(supervisorTypeId) {
        var options = '';
        $('#supervisor_id').attr('disabled', true).html(options).val('').change();
        if (supervisorTypeId.length > 0) {
            $.get('{{ url('admin/users/get-users') }}?is_create_user=1&supervisor_type_id=' + supervisorTypeId,
                function(res) {
                    res.forEach(data => {
                        var selected = defaultSupervisorId == data.id ? 'selected' : '';
                        options += '<option value="' + data.id + '" ' + selected + '>' + data.name +
                            '</option>';
                    });
                    $('#supervisor_id').attr('disabled', false).html(options).change();
                })
        } else {
            $('#supervisor_id').attr('disabled', true).html(options).val('').change();
        }

        if (supervisorTypeId == maxSupervisorTypeId) {
            $.get("{{ url('admin/channels/get-channels') }}", function(
                res) {
                res.forEach(data => {
                    options += '<option value="' + data.id + '">' + data.name +
                        '</option>';
                });
                $('#channel_ids').attr('disabled', false).html(options).change();
            })
        }
    }

    getSupervisors(defaultSupervisorTypeId);

    $('body').on('change', '#supervisor_type_id', function() {
        getSupervisors($(this).val())
    });

    function setChannels(supervisorId = null) {
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
            });
        } else {
            $('#channel_ids').attr('disabled', true).html(options).val('').change();
        }
    }

    $('#supervisor_id').on('change', function() {
        setChannels($(this).val());
    });
</script>
