@extends('layouts.admin')
@section('content')
    <form method="POST" action="{{ route("admin.locations.update", [$location->id]) }}" class="form-loading">
        @method('PUT')
        @csrf
        <div class="card">
            <div class="card-header">
                {{ trans('global.edit') }} {{ trans('cruds.location.title_singular') }}
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="required" for="company_id">{{ trans('cruds.product.fields.company') }}</label>
                    <select class="form-control select2 {{ $errors->has('company') ? 'is-invalid' : '' }}"
                            name="company_id" id="company_id" required>
                        @foreach($companies as $id => $company)
                            <option value="{{ $id }}" {{ $location->company_id == $id ? 'selected' : '' }}>{{ $company }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('company'))
                        <span class="text-danger">{{ $errors->first('company') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.product.fields.company_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="orlan_id" class="required">Orlansoft ID</label>
                    <input class="form-control {{ $errors->has('orlan_id') ? 'is-invalid' : '' }}" type="text" name="orlan_id" id="orlan_id" value="{{ $location->orlan_id }}" required>
                    @if($errors->has('orlan_id'))
                        <span class="text-danger">{{ $errors->first('orlan_id') }}</span>
                    @endif
                </div>
                <x-input key='name' :model='$location'></x-input>
                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection

