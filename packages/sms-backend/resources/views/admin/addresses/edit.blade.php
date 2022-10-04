@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.address.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.addresses.update", [$address->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="address_line_1">{{ trans('cruds.address.fields.address_line_1') }}</label>
                <input class="form-control {{ $errors->has('address_line_1') ? 'is-invalid' : '' }}" type="text" name="address_line_1" id="address_line_1" value="{{ old('address_line_1', $address->address_line_1) }}" required>
                @if($errors->has('address_line_1'))
                    <span class="text-danger">{{ $errors->first('address_line_1') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.address.fields.address_line_1_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="address_line_2">{{ trans('cruds.address.fields.address_line_2') }}</label>
                <input class="form-control {{ $errors->has('address_line_2') ? 'is-invalid' : '' }}" type="text" name="address_line_2" id="address_line_2" value="{{ old('address_line_2', $address->address_line_2) }}">
                @if($errors->has('address_line_2'))
                    <span class="text-danger">{{ $errors->first('address_line_2') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.address.fields.address_line_2_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="address_line_3">{{ trans('cruds.address.fields.address_line_3') }}</label>
                <input class="form-control {{ $errors->has('address_line_3') ? 'is-invalid' : '' }}" type="text" name="address_line_3" id="address_line_3" value="{{ old('address_line_3', $address->address_line_3) }}">
                @if($errors->has('address_line_3'))
                    <span class="text-danger">{{ $errors->first('address_line_3') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.address.fields.address_line_3_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="city">{{ trans('cruds.address.fields.city') }}</label>
                <input class="form-control {{ $errors->has('city') ? 'is-invalid' : '' }}" type="text" name="city" id="city" value="{{ old('city', $address->city) }}">
                @if($errors->has('city'))
                    <span class="text-danger">{{ $errors->first('city') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.address.fields.city_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="country">{{ trans('cruds.address.fields.country') }}</label>
                <input class="form-control {{ $errors->has('country') ? 'is-invalid' : '' }}" type="text" name="country" id="country" value="{{ old('country', $address->country) }}">
                @if($errors->has('country'))
                    <span class="text-danger">{{ $errors->first('country') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.address.fields.country_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="province">{{ trans('cruds.address.fields.province') }}</label>
                <input class="form-control {{ $errors->has('province') ? 'is-invalid' : '' }}" type="text" name="province" id="province" value="{{ old('province', $address->province) }}">
                @if($errors->has('province'))
                    <span class="text-danger">{{ $errors->first('province') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.address.fields.province_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.address.fields.type') }}</label>
                <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type" id="type" required>
                    <option value disabled {{ old('type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(\App\Enums\AddressType::getInstances() as $enum)
                        <option value="{{ $enum->value }}" {{ old('type', $address->type->value) === $enum->value ? 'selected' : '' }}>{{ $enum->description }}</option>
                    @endforeach
                </select>
                @if($errors->has('type'))
                    <span class="text-danger">{{ $errors->first('type') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.address.fields.type_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="phone">{{ trans('cruds.address.fields.phone') }}</label>
                <input class="form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}" type="text" name="phone" id="phone" value="{{ old('phone', $address->phone) }}">
                @if($errors->has('phone'))
                    <span class="text-danger">{{ $errors->first('phone') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.address.fields.phone_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="customer_id">{{ trans('cruds.address.fields.customer') }}</label>
                <select class="form-control {{ $errors->has('customer_id') ? 'is-invalid' : '' }}" name="customer_id" id="customer_id" required>
                    <option value="{{ $address->customer->id ?? '' }}">{{ $address->customer->first_name .' '. $address->customer->last_name }}</option>
                </select>
                @if($errors->has('customer_id'))
                <span class="text-danger">{{ $errors->first('customer_id') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.user_helper') }}</span>
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
@push('js')
<script type="text/javascript">
	$(function () {
		$('#customer_id').select2({
			placeholder: 'Select sales',
			minimumInputLength: 2,
			ajax: {
				url: '{{ route("admin.customers.getCustomers") }}',
				dataType: 'json',
				delay: 250,
				processResults: function (data) {
					return {
						results:  $.map(data, function (item) {
							return {
								text: item.name,
								id: item.id
							}
						})
					};
				},
				cache: true
			}
		});
	});
</script>
@endpush
