@extends('layouts.admin')
@section('content')
    <form method="POST" action="{{ route("admin.sms-channels.update", [$smsChannel->id]) }}" class="form-loading">
        @method('PUT')
        @csrf
        <div class="card">
            <div class="card-header">
                {{ trans('global.edit') }} {{ trans('cruds.smsChannel.title_singular') }}
            </div>
            <div class="card-body">
                <x-input key='name' :model='$smsChannel'></x-input>
                <div class="form-group">
                    <label class="required" for="channel_id">Channel</label>
                    <select class="form-control select2 {{ $errors->has('channel_id') ? 'is-invalid' : '' }}" name="channel_id" id="channel_id" required>
                        @foreach($channels as $id => $name)
                            <option value="{{ $id }}" {{ $smsChannel->channel_id == $id ? 'selected' : '' }}>{{ $name }}</option>
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
            </div>
        </div>
    </form>
@endsection

