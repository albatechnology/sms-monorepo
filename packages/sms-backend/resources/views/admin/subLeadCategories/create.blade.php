@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.subLeadCategory.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.sub-lead-categories.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="required">{{ trans('cruds.leadCategory.title_singular') }}</label>
                    <select name="lead_category_id" required class="form-control">
                        @foreach ($leadCategories as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('lead_category_id'))
                        <span class="text-danger">{{ $errors->first('lead_category_id') }}</span>
                    @endif
                </div>
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.leadCategory.fields.name') }}</label>
                    <input required class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text"
                        name="name" id="name" value="{{ old('name', '') }}" required>
                    @if ($errors->has('name'))
                        <span class="text-danger">{{ $errors->first('name') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.leadCategory.fields.name_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="description">{{ trans('cruds.leadCategory.fields.description') }}</label>
                    <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}"
                        name="description" id="description">{!! old('description') !!}</textarea>
                    @if ($errors->has('description'))
                        <span class="text-danger">{{ $errors->first('description') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.leadCategory.fields.description_helper') }}</span>
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
