\@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.activity.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.activities.store") }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="required" for="user_id">{{ trans('cruds.activity.fields.user') }}</label>
                    <select class="form-control select2 {{ $errors->has('user') ? 'is-invalid' : '' }}" name="user_id"
                            id="user_id" required>
                        @foreach($users as $id => $user)
                            <option value="{{ $id }}" {{ old('user_id') == $id ? 'selected' : '' }}>{{ $user }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('user'))
                        <span class="text-danger">{{ $errors->first('user') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.activity.fields.user_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="lead_id">{{ trans('cruds.activity.fields.lead') }}</label>
                    <select class="form-control select2 {{ $errors->has('lead') ? 'is-invalid' : '' }}" name="lead_id"
                            id="lead_id" required>
                        @foreach($leads as $id => $lead)
                            <option value="{{ $id }}" {{ old('lead_id') == $id ? 'selected' : '' }}>{{ $lead }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('lead'))
                        <span class="text-danger">{{ $errors->first('lead') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.activity.fields.lead_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required"
                           for="follow_up_datetime">{{ trans('cruds.activity.fields.follow_up_datetime') }}</label>
                    <input class="form-control datetime {{ $errors->has('follow_up_datetime') ? 'is-invalid' : '' }}"
                           type="text" name="follow_up_datetime" id="follow_up_datetime"
                           value="{{ old('follow_up_datetime') }}" required>
                    @if($errors->has('follow_up_datetime'))
                        <span class="text-danger">{{ $errors->first('follow_up_datetime') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.activity.fields.follow_up_datetime_helper') }}</span>
                </div>

                <x-enum key='follow_up_method' :model='app(\App\Models\Activity::class)'></x-enum>

                <div class="form-group">
                    <label for="feedback">{{ trans('cruds.activity.fields.feedback') }}</label>
                    <textarea class="form-control {{ $errors->has('feedback') ? 'is-invalid' : '' }}" name="feedback"
                              id="feedback">{{ old('feedback') }}</textarea>
                    @if($errors->has('feedback'))
                        <span class="text-danger">{{ $errors->first('feedback') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.activity.fields.feedback_helper') }}</span>
                </div>
                <div class="form-group">
                    <label>{{ trans('cruds.activity.fields.status') }}</label>
                    <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status"
                            id="status">
                        <option value
                                disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                        @foreach(App\Models\Activity::STATUS_SELECT as $key => $label)
                            <option value="{{ $key }}" {{ old('status', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('status'))
                        <span class="text-danger">{{ $errors->first('status') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.activity.fields.status_helper') }}</span>
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