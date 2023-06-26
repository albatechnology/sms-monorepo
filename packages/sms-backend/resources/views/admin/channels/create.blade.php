@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.channel.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.channels.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.channel.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                @if($errors->has('name'))
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.channel.fields.name_helper') }}</span>
            </div>
            {{-- <div class="form-group">
                <label class="required" for="channel_category_id">{{ trans('cruds.channel.fields.channel_category') }}</label>
                <select class="form-control select2 {{ $errors->has('channel_category') ? 'is-invalid' : '' }}" name="channel_category_id" id="channel_category_id" required>
                    @foreach($channel_categories as $id => $channel_category)
                        <option value="{{ $id }}" {{ old('channel_category_id') == $id ? 'selected' : '' }}>{{ $channel_category }}</option>
                    @endforeach
                </select>
                @if($errors->has('channel_category'))
                    <span class="text-danger">{{ $errors->first('channel_category') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.channel.fields.channel_category_helper') }}</span>
            </div> --}}
            {{-- <div class="form-group">
                <label class="required" for="company_id">{{ trans('cruds.channel.fields.company') }}</label>
                <select class="form-control select2 {{ $errors->has('company') ? 'is-invalid' : '' }}" name="company_id" id="company_id" required>
                    @foreach($companies as $id => $company)
                        <option value="{{ $id }}" {{ old('company_id') == $id ? 'selected' : '' }}>{{ $company }}</option>
                    @endforeach
                </select>
                @if($errors->has('company'))
                    <span class="text-danger">{{ $errors->first('company') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.channel.fields.company_helper') }}</span>
            </div> --}}
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection
