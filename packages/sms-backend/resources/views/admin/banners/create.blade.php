@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.banner.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.banners.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="bannerable_type">{{ trans('cruds.banner.fields.bannerable_type') }}</label>
                <input class="form-control {{ $errors->has('bannerable_type') ? 'is-invalid' : '' }}" type="text" name="bannerable_type" id="bannerable_type" value="{{ old('bannerable_type', '') }}" required>
                @if($errors->has('bannerable_type'))
                    <span class="text-danger">{{ $errors->first('bannerable_type') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.banner.fields.bannerable_type_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="bannerable">{{ trans('cruds.banner.fields.bannerable') }}</label>
                <input class="form-control {{ $errors->has('bannerable') ? 'is-invalid' : '' }}" type="number" name="bannerable" id="bannerable" value="{{ old('bannerable', '') }}" step="1" required>
                @if($errors->has('bannerable'))
                    <span class="text-danger">{{ $errors->first('bannerable') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.banner.fields.bannerable_helper') }}</span>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('is_active') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="is_active" value="0">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', 0) == 1 || old('is_active') === null ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">{{ trans('cruds.banner.fields.is_active') }}</label>
                </div>
                @if($errors->has('is_active'))
                    <span class="text-danger">{{ $errors->first('is_active') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.banner.fields.is_active_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="start_time">{{ trans('cruds.banner.fields.start_time') }}</label>
                <input class="form-control datetime {{ $errors->has('start_time') ? 'is-invalid' : '' }}" type="text" name="start_time" id="start_time" value="{{ old('start_time') }}">
                @if($errors->has('start_time'))
                    <span class="text-danger">{{ $errors->first('start_time') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.banner.fields.start_time_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="end_time">{{ trans('cruds.banner.fields.end_time') }}</label>
                <input class="form-control {{ $errors->has('end_time') ? 'is-invalid' : '' }}" type="text" name="end_time" id="end_time" value="{{ old('end_time', '') }}">
                @if($errors->has('end_time'))
                    <span class="text-danger">{{ $errors->first('end_time') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.banner.fields.end_time_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="company_id">{{ trans('cruds.banner.fields.company') }}</label>
                <select class="form-control select2 {{ $errors->has('company') ? 'is-invalid' : '' }}" name="company_id" id="company_id" required>
                    @foreach($companies as $id => $company)
                        <option value="{{ $id }}" {{ old('company_id') == $id ? 'selected' : '' }}>{{ $company }}</option>
                    @endforeach
                </select>
                @if($errors->has('company'))
                    <span class="text-danger">{{ $errors->first('company') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.banner.fields.company_helper') }}</span>
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