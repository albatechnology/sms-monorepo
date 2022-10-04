@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.user.title_singular') }}
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="orlan_user_id">Orlansoft ID</label>
                    <input class="form-control {{ $errors->has('orlan_user_id') ? 'is-invalid' : '' }}" type="text" name="orlan_user_id" id="orlan_user_id" value="{{ old('orlan_user_id', '') }}">
                    @if($errors->has('orlan_user_id'))
                        <span class="text-danger">{{ $errors->first('orlan_user_id') }}</span>
                    @endif
                </div>
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.user.fields.name') }}</label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name"
                        id="name" value="{{ old('name', '') }}" required>
                    @if ($errors->has('name'))
                        <span class="text-danger">{{ $errors->first('name') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.user.fields.name_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="email">{{ trans('cruds.user.fields.email') }}</label>
                    <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="email" name="email"
                        id="email" value="{{ old('email') }}" required>
                    @if ($errors->has('email'))
                        <span class="text-danger">{{ $errors->first('email') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.user.fields.email_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="password">{{ trans('cruds.user.fields.password') }}</label>
                    <input class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" type="password"
                        name="password" id="password" required>
                    @if ($errors->has('password'))
                        <span class="text-danger">{{ $errors->first('password') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.user.fields.password_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="roles">{{ trans('cruds.user.fields.roles') }}</label>
                    <div style="padding-bottom: 4px">
                        <span class="btn btn-info btn-xs select-all"
                            style="border-radius: 0">{{ trans('global.select_all') }}</span>
                        <span class="btn btn-info btn-xs deselect-all"
                            style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                    </div>
                    <select class="form-control select2 {{ $errors->has('roles') ? 'is-invalid' : '' }}" name="roles[]"
                        id="roles" multiple>
                        @foreach ($roles as $id => $roles)
                            <option value="{{ $id }}" {{ in_array($id, old('roles', [])) ? 'selected' : '' }}>
                                {{ $roles }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('roles'))
                        <span class="text-danger">{{ $errors->first('roles') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.user.fields.roles_helper') }}</span>
                </div>
                <div x-data="{ type: ''}">
                    <!-- type -->
                    <div class="form-group">
                        <label class="required">{{ trans('cruds.user.fields.type') }}</label>
                        <select x-model="type" class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}"
                            name="type" id="type" required>
                            <option value disabled {{ old('type', null) === null ? 'selected' : '' }}>
                                {{ trans('global.pleaseSelect') }}</option>
                            @foreach (App\Enums\UserType::getInstances() as $enum)
                                <option value="{{ $enum->value }}"
                                    {{ old('type', App\Enums\UserType::getDefaultValue()) === (string) $enum->value ? 'selected' : '' }}>
                                    {{ $enum->description }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('type'))
                            <span class="text-danger">{{ $errors->first('type') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.user.fields.type_helper') }}</span>
                    </div>
                    <!-- supervisor_type_id -->
                    <template x-if="type == '{{ \App\Enums\UserType::SUPERVISOR }}'">
                        <div class="form-group">
                            <label class="required"
                                for="supervisor_type_id">{{ trans('cruds.user.fields.supervisor_type') }}</label>
                            <select
                                class="form-control select2 {{ $errors->has('supervisor_type') ? 'is-invalid' : '' }}"
                                name="supervisor_type_id" id="supervisor_type_id">
                                @foreach ($supervisor_types as $id => $supervisor_type)
                                    <option value="{{ $id }}"
                                        {{ old('supervisor_type_id') == $id ? 'selected' : '' }}>
                                        {{ $supervisor_type }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('supervisor_type'))
                                <span class="text-danger">{{ $errors->first('supervisor_type') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.user.fields.supervisor_type_helper') }}</span>
                        </div>
                    </template>
                    <!-- supervisor_id -->
                    <div class="form-group">
                        <label for="supervisor_id">{{ trans('cruds.user.fields.supervisor') }}</label>
                        <select class="form-control select2 {{ $errors->has('supervisor') ? 'is-invalid' : '' }}"
                            name="supervisor_id" id="supervisor_id">
                            @foreach ($supervisors as $id => $supervisor)
                                <option value="{{ $id }}" {{ old('supervisor_id') == $id ? 'selected' : '' }}>
                                    {{ $supervisor }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('supervisor'))
                            <span class="text-danger">{{ $errors->first('supervisor') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.user.fields.supervisor_helper') }}</span>
                    </div>
                    <!-- companies -->
                    <template x-if="type == '{{ \App\Enums\UserType::DIRECTOR }}' || type == '{{ \App\Enums\UserType::DigitalMarketing }}'">
                        <div class="form-group">
                            <label class="required" for="company_ids">{{ trans('cruds.user.fields.company') }}</label>
                            <select class="form-control select2 {{ $errors->has('company_ids') ? 'is-invalid' : '' }}" name="company_ids[]" id="company_ids" multiple>
                                @foreach ($companies as $id => $name)
                                    <option value="{{ $id }}"
                                        {{ in_array($id, old('company_ids', [])) ? 'selected' : '' }}>{{ $name }}
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
                    </template>
                    <template x-if="type == '{{ \App\Enums\UserType::DEFAULT }}' || type == '{{ \App\Enums\UserType::SALES }}' || type == '{{ \App\Enums\UserType::SUPERVISOR }}'">
                        <div class="form-group">
                            <label class="required" for="company_id">{{ trans('cruds.user.fields.company') }}</label>
                            <select class="form-control select2 {{ $errors->has('company_id') ? 'is-invalid' : '' }}" name="company_id" id="company_id">
                                @foreach ($companies as $id => $name)
                                    <option value="{{ $id }}"
                                        {{ old('company_id') == $id ? 'selected' : '' }}>{{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('company_id'))
                                <span class="text-danger">{{ $errors->first('company_id') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.user.fields.company_helper') }}</span>
                        </div>
                        <script>
                            $('.select2').select2();
                        </script>
                    </template>
                    <div class="form-group">
                        <label for="channels">{{ trans('cruds.user.fields.channels') }}</label>
                        <div style="padding-bottom: 4px">
                            <span class="btn btn-info btn-xs select-all"
                                style="border-radius: 0">{{ trans('global.select_all') }}</span>
                            <span class="btn btn-info btn-xs deselect-all"
                                style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                        </div>
                        <select class="form-control select2 {{ $errors->has('channels') ? 'is-invalid' : '' }}"
                            name="channels[]" id="channels" multiple disabled data-placeholder="Select channels">
                        </select>
                        @if ($errors->has('channels'))
                            <span class="text-danger">{{ $errors->first('channels') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.user.fields.channels_helper') }}</span>
                    </div>
                </div>
                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function dropdown() {
            return {
                type: null,
                set(type) {
                    this.type = type
                },
                close() {
                    this.show = false
                },
                isOpen() {
                    return this.show === true
                },
            }
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var companyId = null;
        $('body').on('change', '#company_id', function() {
            $('#channels').attr('disabled', true).html('');
            companyId = $(this).val();
            $.post("{{ url('admin/users/get-channels') }}/" + companyId, function(html) {
                if (companyId) {
                    $('#channels').attr('disabled', false).html(html);
                } else {
                    $('#channels').attr('disabled', true).html('');
                }
            });
        });
    </script>
@endsection
