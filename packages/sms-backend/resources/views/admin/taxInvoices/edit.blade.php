@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.taxInvoice.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.tax-invoices.update", [$taxInvoice->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="customer_id">{{ trans('cruds.taxInvoice.fields.customer') }}</label>
                <select class="form-control select2 {{ $errors->has('customer') ? 'is-invalid' : '' }}" name="customer_id" id="customer_id" required>
                    @foreach($customers as $id => $customer)
                        <option value="{{ $id }}" {{ (old('customer_id') ? old('customer_id') : $taxInvoice->customer->id ?? '') == $id ? 'selected' : '' }}>{{ $customer }}</option>
                    @endforeach
                </select>
                @if($errors->has('customer'))
                    <span class="text-danger">{{ $errors->first('customer') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.taxInvoice.fields.customer_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="company_name">{{ trans('cruds.taxInvoice.fields.company_name') }}</label>
                <input class="form-control {{ $errors->has('company_name') ? 'is-invalid' : '' }}" type="text" name="company_name" id="company_name" value="{{ old('company_name', $taxInvoice->company_name) }}" required>
                @if($errors->has('company_name'))
                    <span class="text-danger">{{ $errors->first('company_name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.taxInvoice.fields.company_name_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="npwp">{{ trans('cruds.taxInvoice.fields.npwp') }}</label>
                <input class="form-control {{ $errors->has('npwp') ? 'is-invalid' : '' }}" type="text" name="npwp" id="npwp" value="{{ old('npwp', $taxInvoice->npwp) }}">
                @if($errors->has('npwp'))
                    <span class="text-danger">{{ $errors->first('npwp') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.taxInvoice.fields.npwp_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="address_id">{{ trans('cruds.taxInvoice.fields.address') }}</label>
                <select class="form-control select2 {{ $errors->has('address') ? 'is-invalid' : '' }}" name="address_id" id="address_id" required>
                    @foreach($addresses as $id => $address)
                        <option value="{{ $id }}" {{ (old('address_id') ? old('address_id') : $taxInvoice->address->id ?? '') == $id ? 'selected' : '' }}>{{ $address }}</option>
                    @endforeach
                </select>
                @if($errors->has('address'))
                    <span class="text-danger">{{ $errors->first('address') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.taxInvoice.fields.address_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="email">{{ trans('cruds.taxInvoice.fields.email') }}</label>
                <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="text" name="email" id="email" value="{{ old('email', $taxInvoice->email) }}">
                @if($errors->has('email'))
                    <span class="text-danger">{{ $errors->first('email') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.taxInvoice.fields.email_helper') }}</span>
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