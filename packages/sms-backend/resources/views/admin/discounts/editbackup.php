@extends('layouts.admin')
@section('content')
<div class="card">
	<div class="card-header">
		{{ trans('global.edit') }} {{ trans('cruds.discount.title_singular') }}
	</div>
	<div class="card-body">
		<form method="POST" action="{{ route("admin.discounts.update", [$discount->id]) }}"
			enctype="multipart/form-data">
			@method('PUT')
			@csrf
			<x-input key='name' :model='$discount'></x-input>
			<x-input key='description' :model='$discount' required="0"></x-input>
			<x-enum key='type' :model='$discount'></x-enum>
			<x-input key='activation_code' :model='$discount' required="0"></x-input>
			<x-input key='value' :model='$discount' type="number"></x-input>
			<x-enum key='scope' :model='$discount'></x-enum>
			<x-input key='start_time' :model='$discount' type="datetime"></x-input>
			<x-input key='end_time' :model='$discount' type="datetime"></x-input>
			<div class="form-group">
				<div class="form-check {{ $errors->has('is_active') ? 'is-invalid' : '' }}">
					<input type="hidden" name="is_active" value="0">
					<input class="form-check-input" type="checkbox" name="is_active" id="is_active"
					value="1" {{ $discount->is_active || old('is_active', 0) === 1 ? 'checked' : '' }}>
					<label class="form-check-label"
					for="is_active">{{ trans('cruds.discount.fields.is_active') }}</label>
				</div>
				@if($errors->has('is_active'))
				<span class="text-danger">{{ $errors->first('is_active') }}</span>
				@endif
				<span class="help-block">{{ trans('cruds.discount.fields.is_active_helper') }}</span>
			</div>
			<x-input key='max_discount_price_per_order' :model='$discount' type="number" required="0"></x-input>
			<x-input key='max_use_per_customer' :model='$discount' type="number" required="0"></x-input>
			<x-input key='min_order_price' :model='$discount' type="number" required="0"></x-input>

			<x-select-company :model='$discount'></x-select-company>
			<div class="form-group">
				<button class="btn btn-danger" type="submit">
					{{ trans('global.save') }}
				</button>
			</div>
		</form>
	</div>
</div>
@endsection
