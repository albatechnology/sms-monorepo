@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.targetSchedule.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.target-schedules.update", [$targetSchedule->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required">{{ trans('cruds.targetSchedule.fields.duration_type') }}</label>
                <select class="form-control {{ $errors->has('duration_type') ? 'is-invalid' : '' }}" name="duration_type" id="duration_type" required>
                    <option value disabled {{ old('duration_type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\TargetSchedule::DURATION_TYPE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('duration_type', $targetSchedule->duration_type) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('duration_type'))
                    <span class="text-danger">{{ $errors->first('duration_type') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.targetSchedule.fields.duration_type_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="start_date">{{ trans('cruds.targetSchedule.fields.start_date') }}</label>
                <input class="form-control date {{ $errors->has('start_date') ? 'is-invalid' : '' }}" type="text" name="start_date" id="start_date" value="{{ old('start_date', $targetSchedule->start_date) }}" required>
                @if($errors->has('start_date'))
                    <span class="text-danger">{{ $errors->first('start_date') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.targetSchedule.fields.start_date_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="target_name">{{ trans('cruds.targetSchedule.fields.target_name') }}</label>
                <input class="form-control {{ $errors->has('target_name') ? 'is-invalid' : '' }}" type="text" name="target_name" id="target_name" value="{{ old('target_name', $targetSchedule->target_name) }}">
                @if($errors->has('target_name'))
                    <span class="text-danger">{{ $errors->first('target_name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.targetSchedule.fields.target_name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="value">{{ trans('cruds.targetSchedule.fields.value') }}</label>
                <input class="form-control {{ $errors->has('value') ? 'is-invalid' : '' }}" type="number" name="value" id="value" value="{{ old('value', $targetSchedule->value) }}" step="1" required>
                @if($errors->has('value'))
                    <span class="text-danger">{{ $errors->first('value') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.targetSchedule.fields.value_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.targetSchedule.fields.target_value_type') }}</label>
                <select class="form-control {{ $errors->has('target_value_type') ? 'is-invalid' : '' }}" name="target_value_type" id="target_value_type" required>
                    <option value disabled {{ old('target_value_type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\TargetSchedule::TARGET_VALUE_TYPE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('target_value_type', $targetSchedule->target_value_type) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('target_value_type'))
                    <span class="text-danger">{{ $errors->first('target_value_type') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.targetSchedule.fields.target_value_type_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.targetSchedule.fields.target_type') }}</label>
                <select class="form-control {{ $errors->has('target_type') ? 'is-invalid' : '' }}" name="target_type" id="target_type" required>
                    <option value disabled {{ old('target_type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\TargetSchedule::TARGET_TYPE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('target_type', $targetSchedule->target_type) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('target_type'))
                    <span class="text-danger">{{ $errors->first('target_type') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.targetSchedule.fields.target_type_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.targetSchedule.fields.target_subject') }}</label>
                <select class="form-control {{ $errors->has('target_subject') ? 'is-invalid' : '' }}" name="target_subject" id="target_subject" required>
                    <option value disabled {{ old('target_subject', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\TargetSchedule::TARGET_SUBJECT_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('target_subject', $targetSchedule->target_subject) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('target_subject'))
                    <span class="text-danger">{{ $errors->first('target_subject') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.targetSchedule.fields.target_subject_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.targetSchedule.fields.target_subject_type') }}</label>
                <select class="form-control {{ $errors->has('target_subject_type') ? 'is-invalid' : '' }}" name="target_subject_type" id="target_subject_type" required>
                    <option value disabled {{ old('target_subject_type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\TargetSchedule::TARGET_SUBJECT_TYPE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('target_subject_type', $targetSchedule->target_subject_type) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('target_subject_type'))
                    <span class="text-danger">{{ $errors->first('target_subject_type') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.targetSchedule.fields.target_subject_type_helper') }}</span>
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