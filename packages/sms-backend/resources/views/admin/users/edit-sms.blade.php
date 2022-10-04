@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.user.title_singular') }}
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.updateSms', $user->id) }}">
                @csrf
                @method('PUT')
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
                    <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="email" name="email"
                        id="email" value="{{ $user->email }}" required>
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
                <div x-data="{ type: '{{$user->type}}'}">
                    <div class="form-group">
                        <label class="required">{{ trans('cruds.user.fields.type') }}</label>
                        <select x-model="type" class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type" id="type" required>
                            <option value="">{{ trans('global.pleaseSelect') }}</option>
                            @foreach ($types as $id => $description)
                                <option value="{{ $id }}" {{ $user->type->value == $id ? 'selected' : '' }}>{{ $description }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('type'))
                            <span class="text-danger">{{ $errors->first('type') }}</span>
                        @endif
                    </div>
                    <template x-if="type == '{{ \App\Enums\UserType::SALES_SMS }}'">
                        <div class="form-group">
                            <label class="required" for="supervisor_id">Supervisor</label>
                            <select class="form-control select2 {{ $errors->has('supervisor_id') ? 'is-invalid' : '' }}" name="supervisor_id" id="supervisor_id">
                                @foreach ($supervisors as $id => $name)
                                    <option value="{{ $id }}" {{ $user->supervisor_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('supervisor_id'))
                                <span class="text-danger">{{ $errors->first('supervisor_id') }}</span>
                            @endif
                        </div>
                    </template>
                    <template x-if="type == '{{ \App\Enums\UserType::SUPERVISOR_SMS }}'">
                        <div class="form-group">
                            <label for="channel_id">Channel</label>
                            <select class="form-control select2 {{ $errors->has('channel_id') ? 'is-invalid' : '' }}" name="channel_id" id="channel_id">
                                @foreach ($channels as $id => $name)
                                    <option value="{{ $id }}" {{ $user->channel_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('channel_id'))
                                <span class="text-danger">{{ $errors->first('channel_id') }}</span>
                            @endif
                        </div>
                    </template>
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
