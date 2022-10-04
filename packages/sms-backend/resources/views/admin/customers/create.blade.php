@extends('layouts.admin')
@section('content')

<form method="POST" action="{{ route("admin.customers.store") }}" enctype="multipart/form-data">
    @csrf
    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.customer.title_singular') }}
        </div>
        <div class="card-body">
            <div class="form-group">
                <label>{{ trans('cruds.customer.fields.title') }}</label>
                <select class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" name="title" id="title">
                    <option value disabled {{ old('title', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Enums\PersonTitle::getInstances() as $enum)
                        <option value="{{ $enum->value }}" {{ old('title', 1) == $enum->value ? 'selected' : '' }}>{{ $enum->description }}</option>
                    @endforeach
                </select>
                @if($errors->has('title'))
                    <span class="text-danger">{{ $errors->first('title') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.customer.fields.title_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="first_name">{{ trans('cruds.customer.fields.first_name') }}</label>
                <input class="form-control {{ $errors->has('first_name') ? 'is-invalid' : '' }}" type="text" name="first_name" id="first_name" value="{{ old('first_name', '') }}" required>
                @if($errors->has('first_name'))
                    <span class="text-danger">{{ $errors->first('first_name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.customer.fields.first_name_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="last_name">{{ trans('cruds.customer.fields.last_name') }}</label>
                <input class="form-control {{ $errors->has('last_name') ? 'is-invalid' : '' }}" type="text" name="last_name" id="last_name" value="{{ old('last_name', '') }}">
                @if($errors->has('last_name'))
                    <span class="text-danger">{{ $errors->first('last_name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.customer.fields.last_name_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="email">{{ trans('cruds.customer.fields.email') }}</label>
                <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="text" name="email" id="email" value="{{ old('email', '') }}">
                @if($errors->has('email'))
                    <span class="text-danger">{{ $errors->first('email') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.customer.fields.email_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="phone">{{ trans('cruds.customer.fields.phone') }}</label>
                <input class="form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}" type="text" name="phone" id="phone" value="{{ old('phone', '') }}">
                @if($errors->has('phone'))
                    <span class="text-danger">{{ $errors->first('phone') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.customer.fields.phone_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="description">{{ trans('cruds.customer.fields.description') }}</label>
                <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" id="description">{{ old('description') }}</textarea>
                @if($errors->has('description'))
                    <span class="text-danger">{{ $errors->first('description') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.customer.fields.description_helper') }}</span>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">Customer Address</div>
        <div class="card-body">
            <div class="form-group">
                <label class="required" for="address_line_1">{{ trans('cruds.address.fields.address_line_1') }}</label>
                <input class="form-control {{ $errors->has('address_line_1') ? 'is-invalid' : '' }}" type="text" name="address_line_1" id="address_line_1" value="{{ old('address_line_1', '') }}" required>
                @if($errors->has('address_line_1'))
                    <span class="text-danger">{{ $errors->first('address_line_1') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.address.fields.address_line_1_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="address_line_2">{{ trans('cruds.address.fields.address_line_2') }}</label>
                <input class="form-control {{ $errors->has('address_line_2') ? 'is-invalid' : '' }}" type="text" name="address_line_2" id="address_line_2" value="{{ old('address_line_2', '') }}">
                @if($errors->has('address_line_2'))
                    <span class="text-danger">{{ $errors->first('address_line_2') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.address.fields.address_line_2_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="address_line_3">{{ trans('cruds.address.fields.address_line_3') }}</label>
                <input class="form-control {{ $errors->has('address_line_3') ? 'is-invalid' : '' }}" type="text" name="address_line_3" id="address_line_3" value="{{ old('address_line_3', '') }}">
                @if($errors->has('address_line_3'))
                    <span class="text-danger">{{ $errors->first('address_line_3') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.address.fields.address_line_3_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="city">{{ trans('cruds.address.fields.city') }}</label>
                <input class="form-control {{ $errors->has('city') ? 'is-invalid' : '' }}" type="text" name="city" id="city" value="{{ old('city', '') }}">
                @if($errors->has('city'))
                    <span class="text-danger">{{ $errors->first('city') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.address.fields.city_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="country">{{ trans('cruds.address.fields.country') }}</label>
                <input class="form-control {{ $errors->has('country') ? 'is-invalid' : '' }}" type="text" name="country" id="country" value="{{ old('country', '') }}">
                @if($errors->has('country'))
                    <span class="text-danger">{{ $errors->first('country') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.address.fields.country_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="province">{{ trans('cruds.address.fields.province') }}</label>
                <input class="form-control {{ $errors->has('province') ? 'is-invalid' : '' }}" type="text" name="province" id="province" value="{{ old('province', '') }}">
                @if($errors->has('province'))
                    <span class="text-danger">{{ $errors->first('province') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.address.fields.province_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.address.fields.type') }}</label>
                <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type" id="type" required>
                    <option value disabled {{ old('type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Enums\AddressType::getInstances() as $enum)
                        <option value="{{ $enum->value }}" {{ old('type', App\Enums\AddressType::ADDRESS) === (string) $enum->value ? 'selected' : '' }}>{{ $enum->description }}</option>
                    @endforeach
                </select>
                @if($errors->has('type'))
                    <span class="text-danger">{{ $errors->first('type') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.address.fields.type_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="address_phone">{{ trans('cruds.address.fields.phone') }}</label>
                <input class="form-control {{ $errors->has('address_phone') ? 'is-invalid' : '' }}" type="text" name="address_phone" id="address_phone" value="{{ old('address_phone', '') }}">
                @if($errors->has('address_phone'))
                    <span class="text-danger">{{ $errors->first('address_phone') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.address.fields.phone_helper') }}</span>
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
