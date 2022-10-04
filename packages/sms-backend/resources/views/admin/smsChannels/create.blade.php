@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.smsChannel.title_singular') }}
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route("admin.sms-channels.store") }}" class="form-loading">
            @csrf
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.smsChannel.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                @if($errors->has('name'))
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.smsChannel.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="channel_id">Channel</label>
                <select class="form-control select2 {{ $errors->has('channel_id') ? 'is-invalid' : '' }}" name="channel_id" id="channel_id" required>
                    @foreach($channels as $id => $name)
                        <option value="{{ $id }}" {{ old('channel_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                @if($errors->has('channel_id'))
                    <span class="text-danger">{{ $errors->first('channel_id') }}</span>
                @endif
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
