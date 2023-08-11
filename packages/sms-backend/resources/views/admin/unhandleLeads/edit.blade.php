@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.assign') }} {{ trans('cruds.unhandleLead.title_singular') }}
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.unhandle-leads.update', [$lead->id]) }}">
                @method('PUT')
                @csrf
                <div class="form-group">
                    <label for="label">{{ trans('cruds.unhandleLead.fields.label') }}</label>
                    <input class="form-control" type="text" disabled value="{{ $lead->label }}">
                </div>
                <div class="form-group">
                    <label class="required">{{ trans('cruds.unhandleLead.fields.user_type') }}</label>
                    <select class="form-control {{ $errors->has('user_type') ? 'is-invalid' : '' }}" name="user_type"
                        id="user_type" required>
                        <option value disabled {{ old('user_type', null) === null ? 'selected' : '' }}>
                            {{ trans('global.pleaseSelect') }}</option>
                        <option value="1" {{ old('type') === 1 ? 'selected' : '' }}>Manager Area / BUM</option>
                        <option value="2" {{ old('type') === 2 ? 'selected' : '' }}>Store Leader</option>
                        <option value="3" {{ old('type') === 3 ? 'selected' : '' }}>Sales</option>
                    </select>
                    @if ($errors->has('type'))
                        <span class="text-danger">{{ $errors->first('type') }}</span>
                    @endif
                </div>
                <div class="form-group">
                    <label class="required">{{ trans('cruds.unhandleLead.fields.assign_to_user') }}</label>
                    <select class="form-control select2 {{ $errors->has('user_id') ? 'is-invalid' : '' }}" name="user_id"
                        id="user_id" required disabled></select>
                    @if ($errors->has('user_id'))
                        <span class="text-danger">{{ $errors->first('user_id') }}</span>
                    @endif
                </div>
                <div class="form-group">
                    <button class="btn btn-danger" type="submit">{{ trans('global.save') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('js')
    <script type="text/javascript">
        $(function() {
            var userType = null;
            $('#user_type').on('change', function() {
                $('#user_id').attr('disabled', true).html('');
                userType = $(this).val();
                $.get('{{ url('admin/unhandle-leads/get-users/' . $lead->channel_id) }}/' +
                    userType,
                    function(html) {
                        if (userType) {
                            $('#user_id').attr('disabled', false).html(html);
                        } else {
                            $('#user_id').attr('disabled', true).html('');
                        }
                    });
            });
        });
    </script>
@endpush
