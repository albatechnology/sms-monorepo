@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.currency.title_singular') }}
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route("admin.currencies.store") }}">
            @csrf
            <div class="form-group">
                <label class="required" for="main_currency">{{ trans('cruds.currency.fields.main_currency') }}</label>
                <input type="text" name="main_currency" class="form-control" value="IDR" readonly>
                {{-- <select name="main_currency" id="main_currency" class="form-control select2" required>
                    @foreach ($currencyList as $key => $value)
                        <option value="{{$key}}" {{ old('main_currency') == $key ? 'selected' : '' }}>{{$value}}</option>
                    @endforeach
                </select> --}}
                @if($errors->has('main_currency'))
                    <span class="text-danger">{{ $errors->first('main_currency') }}</span>
                @endif
            </div>
            <div class="form-group">
                <label class="required" for="foreign_currency">{{ trans('cruds.currency.fields.foreign_currency') }}</label>
                <select name="foreign_currency" id="foreign_currency" class="form-control select2" required>
                    @foreach ($currencyList as $key => $description)
                        <option value="{{$key}}" {{ old('foreign_currency') == $key ? 'selected' : '' }}>{{$description}}</option>
                    @endforeach
                </select>
                @if($errors->has('foreign_currency'))
                    <span class="text-danger">{{ $errors->first('foreign_currency') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.currency.fields.foreign_currency_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="value">{{ trans('cruds.currency.fields.value') }}</label>
                <input class="form-control {{ $errors->has('value') ? 'is-invalid' : '' }}" type="number" name="value" id="value" value="{{ old('value', '') }}" required>
                @if($errors->has('value'))
                    <span class="text-danger">{{ $errors->first('value') }}</span>
                @endif
                {{-- <span class="help-block">{{ trans('cruds.currency.fields.value_helper') }}</span> --}}
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">{{ trans('global.save') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
