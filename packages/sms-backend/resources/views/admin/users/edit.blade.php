@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.user.title_singular') }}
        </div>
        <div class="card-body">
            <form autocomplete="off" method="POST" action="{{ route('admin.users.update', [$user->id]) }}"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.user.fields.name') }}</label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name"
                        id="name" value="{{ $user->name }}" required>
                    @if ($errors->has('name'))
                        <span class="text-danger">{{ $errors->first('name') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.user.fields.name_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="email">{{ trans('cruds.user.fields.email') }}</label>
                    <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="email"
                        name="email" id="email" value="{{ $user->email }}" required>
                    @if ($errors->has('email'))
                        <span class="text-danger">{{ $errors->first('email') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.user.fields.email_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="password">{{ trans('cruds.user.fields.password') }}</label>
                    <input class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" type="password"
                        name="password" id="password">
                    @if ($errors->has('password'))
                        <span class="text-danger">{{ $errors->first('password') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.user.fields.password_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="role">{{ trans('cruds.user.fields.roles') }}</label>

                    <select class="form-control select2 {{ $errors->has('role') ? 'is-invalid' : '' }}" name="role"
                        id="role">
                        @foreach ($roles as $id => $role)
                            <option value="{{ $id }}" @if(count($user->roles) > 0 && $user->roles[0]->id == $id) selected @endif>
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
                                {{ $user->type->value == $enum->value ? 'selected' : '' }}>
                                {{ $enum->description }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('type'))
                        <span class="text-danger">{{ $errors->first('type') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.user.fields.type_helper') }}</span>
                </div>
                <div id="extra-form"></div>
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

        function setExtraForm(type) {
            if (type == 1) {
                $('#extra-form').load("{{ url('admin/users/includes/default/' . $user->id) }}");
            } else if (type == 2) {
                $('#extra-form').load("{{ url('admin/users/includes/sales/' . $user->id) }}");
            } else if (type == 3) {
                $('#extra-form').load("{{ url('admin/users/includes/supervisor/' . $user->id) }}");
            } else if (type == 4) {
                $('#extra-form').load("{{ url('admin/users/includes/director/' . $user->id) }}");
            } else {
                $('#extra-form').html("");
            }

            if (type) {
                $(":submit").attr('disabled', false);
            } else {
                $(":submit").attr('disabled', true);
            }
        }

        setExtraForm('{{ $user?->type?->value }}')

        $('#type').on('change', function() {
            setExtraForm($(this).val());
        });
    </script>
@endsection
