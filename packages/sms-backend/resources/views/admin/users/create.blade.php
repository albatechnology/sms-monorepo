@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.user.title_singular') }}
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data" class="form-loading">
                @csrf
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
                    <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="email"
                        name="email" id="email" value="{{ old('email') }}" required>
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
                    <label for="role">{{ trans('cruds.user.fields.roles') }}</label>
                    {{-- <div style="padding-bottom: 4px">
                        <span class="btn btn-info btn-xs select-all"
                            style="border-radius: 0">{{ trans('global.select_all') }}</span>
                        <span class="btn btn-info btn-xs deselect-all"
                            style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                    </div> --}}
                    <select class="form-control select2 {{ $errors->has('role') ? 'is-invalid' : '' }}" name="role"
                        id="role">
                        @foreach ($roles as $id => $role)
                            <option value="{{ $id }}" {{ in_array($id, old('role', [])) ? 'selected' : '' }}>
                                {{ $role }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('role'))
                        <span class="text-danger">{{ $errors->first('role') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.user.fields.roles_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required">{{ trans('cruds.user.fields.type') }}</label>
                    <select x-model="type" class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}"
                        name="type" id="type" required>
                        <option value="">{{ trans('global.pleaseSelect') }}</option>
                        @foreach (App\Enums\UserType::getInstances() as $enum)
                            <option value="{{ $enum->value }}"
                                {{ old('type') === (string) $enum->value ? 'selected' : '' }}>
                                {{ $enum->description }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('type'))
                        <span class="text-danger">{{ $errors->first('type') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.user.fields.type_helper') }}</span>
                </div>
                <div id="extra-form"></div>
                {{-- <div x-data="{ type: '' }"> --}}
                <!-- type -->

                {{-- <template
                        x-if="type == '{{ \App\Enums\UserType::DIRECTOR }}' || type == '{{ \App\Enums\UserType::DIGITAL_MARKETING }}'">
                        <div class="form-group">
                            <label class="required" for="company_ids">{{ trans('cruds.user.fields.company') }}</label>
                            <select class="form-control select2 {{ $errors->has('company_ids') ? 'is-invalid' : '' }}"
                                name="company_ids[]" id="company_ids" multiple>
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
                    </template> --}}
                <!-- supervisor_type_id -->
                {{-- <template x-if="type == '{{ \App\Enums\UserType::SUPERVISOR }}'">
                        <div class="form-group">
                            <label class="required" for="company_id">{{ trans('cruds.user.fields.company') }}</label>
                            <select class="form-control select2 {{ $errors->has('company_id') ? 'is-invalid' : '' }}"
                                name="company_id" id="company_id">
                                @foreach ($companies as $id => $name)
                                    <option value="{{ $id }}" {{ old('company_id') == $id ? 'selected' : '' }}>
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
                            <label class="required"
                                for="supervisor_type_id">{{ trans('cruds.user.fields.supervisor_type') }}</label>
                            <select class="form-control select2 {{ $errors->has('supervisor_type') ? 'is-invalid' : '' }}"
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
                    </template> --}}

                <!-- companies -->
                {{-- <template
                        x-if="type == '{{ \App\Enums\UserType::DEFAULT }}' || type == '{{ \App\Enums\UserType::SALES }}' }}'">
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

                    </template> --}}

                {{-- <div class="form-group">
                        <label for="channel_ids">{{ trans('cruds.user.fields.channels') }}</label>
                        <div style="padding-bottom: 4px">
                            <span class="btn btn-info btn-xs select-all"
                                style="border-radius: 0">{{ trans('global.select_all') }}</span>
                            <span class="btn btn-info btn-xs deselect-all"
                                style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                        </div>
                        <select class="form-control select2 {{ $errors->has('channel_ids') ? 'is-invalid' : '' }}"
                            name="channel_ids[]" id="channel_ids" multiple disabled data-placeholder="Select channel_ids">
                        </select>
                        @if ($errors->has('channel_ids'))
                            <span class="text-danger">{{ $errors->first('channel_ids') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.user.fields.channels_helper') }}</span>
                    </div>

                    <div class="form-group">
                        <label>Product Brand</label>
                        <div class="mb-1">
                            <button type="button" class="btn btn-success btn-xs btnSelectAll">Select All</button>
                            <button type="button" class="btn btn-success btn-xs btnDeselectAll">Deselect All</button>
                        </div>
                        <select name="product_brand_ids[]" id="product_brand_ids"
                            class="form-control select2 @error('product_brand_ids') is-invalid @enderror" multiple
                            disabled>
                        </select>
                        @error('product_brand_ids')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div> --}}
                {{-- </div> --}}
                <div class="form-group">
                    <button class="btn btn-danger" type="submit" disabled>
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

        $('#type').on('change', function() {
            var type = $(this).val();

            if (type == 1) {
                $('#extra-form').load("{{ url('admin/users/includes/default') }}");
            } else if (type == 2 || type == 8) {
                $('#extra-form').load("{{ url('admin/users/includes/sales') }}");
            } else if (type == 3) {
                $('#extra-form').load("{{ url('admin/users/includes/supervisor') }}");
            } else {
                $('#extra-form').html("");
            }

            if (type) {
                $(":submit").attr('disabled', false);
            } else {
                $(":submit").attr('disabled', true);
            }
        });

    </script>
@endsection
