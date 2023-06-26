@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.interiorDesign.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.interior-designs.update", [$interiorDesign->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.interiorDesign.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $interiorDesign->name) }}" required>
                @if($errors->has('name'))
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.interiorDesign.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="bum_id">{{ trans('cruds.interiorDesign.fields.bum') }}</label>
                <select class="form-control select2 {{ $errors->has('bum_id') ? 'is-invalid' : '' }}"
                        name="bum_id" id="bum_id">
                    @foreach($bums as $id => $bum)
                        <option value="{{ $id }}" {{ old('bum_id', $interiorDesign->bum_id) == $id ? 'selected' : '' }}>{{ $bum }}</option>
                    @endforeach
                </select>
                @if($errors->has('bum_id'))
                    <span class="text-danger">{{ $errors->first('bum_id') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.interiorDesign.fields.bum_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="sales_id">{{ trans('cruds.interiorDesign.fields.sales') }}</label>
                <select class="form-control select2 {{ $errors->has('sales_id') ? 'is-invalid' : '' }}"
                        name="sales_id" id="sales_id">
                    @foreach($saleses as $id => $sales)
                        <option value="{{ $id }}" {{ old('sales_id', $interiorDesign->sales_id) == $id ? 'selected' : '' }}>{{ $sales }}</option>
                    @endforeach
                </select>
                @if($errors->has('sales_id'))
                    <span class="text-danger">{{ $errors->first('sales_id') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.interiorDesign.fields.sales_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="company">{{ trans('cruds.interiorDesign.fields.company') }}</label>
                <input class="form-control {{ $errors->has('company') ? 'is-invalid' : '' }}" type="text" name="company" id="company" value="{{ old('company', $interiorDesign->company) }}">
                @if($errors->has('company'))
                    <span class="text-danger">{{ $errors->first('company') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.interiorDesign.fields.company_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="owner">{{ trans('cruds.interiorDesign.fields.owner') }}</label>
                <input class="form-control {{ $errors->has('owner') ? 'is-invalid' : '' }}" type="text" name="owner" id="owner" value="{{ old('owner', $interiorDesign->owner) }}">
                @if($errors->has('owner'))
                    <span class="text-danger">{{ $errors->first('owner') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.interiorDesign.fields.owner_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="npwp">{{ trans('cruds.interiorDesign.fields.npwp') }}</label>
                <input class="form-control {{ $errors->has('npwp') ? 'is-invalid' : '' }}" type="text" name="npwp" id="npwp" value="{{ old('npwp', $interiorDesign->npwp) }}">
                @if($errors->has('npwp'))
                    <span class="text-danger">{{ $errors->first('npwp') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.interiorDesign.fields.npwp_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="address">{{ trans('cruds.interiorDesign.fields.address') }}</label>
                <textarea class="form-control {{ $errors->has('address') ? 'is-invalid' : '' }}" name="address" id="address">{{ old('address', $interiorDesign->address) }}</textarea>
                @if($errors->has('address'))
                    <span class="text-danger">{{ $errors->first('address') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.interiorDesign.fields.address_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="phone">{{ trans('cruds.interiorDesign.fields.phone') }}</label>
                <input class="form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}" type="text" name="phone" id="phone" value="{{ old('phone', $interiorDesign->phone) }}">
                @if($errors->has('phone'))
                    <span class="text-danger">{{ $errors->first('phone') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.interiorDesign.fields.phone_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="email">{{ trans('cruds.interiorDesign.fields.email') }}</label>
                <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="email" name="email" id="email" value="{{ old('email', $interiorDesign->email) }}">
                @if($errors->has('email'))
                <span class="text-danger">{{ $errors->first('email') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.interiorDesign.fields.email_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="religion_id">{{ trans('cruds.interiorDesign.fields.religion') }}</label>
                <select class="form-control select2 {{ $errors->has('religion_id') ? 'is-invalid' : '' }}"
                        name="religion_id" id="religion_id">
                    @foreach($religions as $id => $religion)
                        <option value="{{ $id }}" {{ old('religion_id', $interiorDesign->religion_id) == $id ? 'selected' : '' }}>{{ $religion }}</option>
                    @endforeach
                </select>
                @if($errors->has('religion_id'))
                    <span class="text-danger">{{ $errors->first('religion_id') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.interiorDesign.fields.religion_helper') }}</span>
            </div>

            <div class="form-group">
                <label for="bank_account_name">{{ trans('cruds.interiorDesign.fields.bank_account_name') }}</label>
                <input class="form-control {{ $errors->has('bank_account_name') ? 'is-invalid' : '' }}" type="text" name="bank_account_name" id="bank_account_name" value="{{ old('bank_account_name', $interiorDesign->bank_account_name) }}">
                @if($errors->has('bank_account_name'))
                    <span class="text-danger">{{ $errors->first('bank_account_name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.interiorDesign.fields.bank_account_name_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="bank_account_holder">{{ trans('cruds.interiorDesign.fields.bank_account_holder') }}</label>
                <input class="form-control {{ $errors->has('bank_account_holder') ? 'is-invalid' : '' }}" type="text" name="bank_account_holder" id="bank_account_holder" value="{{ old('bank_account_holder', $interiorDesign->bank_account_holder) }}">
                @if($errors->has('bank_account_holder'))
                    <span class="text-danger">{{ $errors->first('bank_account_holder') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.interiorDesign.fields.bank_account_holder_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="bank_account_number">{{ trans('cruds.interiorDesign.fields.bank_account_number') }}</label>
                <input class="form-control {{ $errors->has('bank_account_number') ? 'is-invalid' : '' }}" type="text" name="bank_account_number" id="bank_account_number" value="{{ old('bank_account_number', $interiorDesign->bank_account_number) }}">
                @if($errors->has('bank_account_number'))
                    <span class="text-danger">{{ $errors->first('bank_account_number') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.interiorDesign.fields.bank_account_number_helper') }}</span>
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
