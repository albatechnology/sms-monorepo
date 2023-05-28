<div>
	{{-- <div class="form-group">
		<label class="required" for="company_id">{{ trans('cruds.generic.fields.company') }}</label>
		<select wire:model="company_id" class="form-control {{ $errors->has('company_id') ? 'is-invalid' : '' }}"
			name="company_id" id="company_id">
			<option disabled selected value="">-- select company --</option>
			@foreach(App\Models\Company::tenanted()->get() as $company)
			<option value="{{ $company->id }}" {{ $company->id == old('company_id') ? 'selected' : '' }}>{{ $company->name }}</option>
			@endforeach
		</select>
		@if($errors->has('company_id'))
		<span class="text-danger">{{ $errors->first('company_id') }}</span>
		@endif
		<span class="help-block">{{ trans('cruds.generic.fields.company_helper') }}</span>
	</div> --}}
	<x-input key='name' :model='app(\App\Models\Discount::class)'></x-input>
	<x-input key='description' :model='app(\App\Models\Discount::class)' required="0"></x-input>
	<x-enum key='type' :model='app(\App\Models\Discount::class)'></x-enum>
	<x-input key='activation_code' :model='app(\App\Models\Discount::class)' required="0"></x-input>
    <div class="form-group">
        <label class="required" for="stock">Value</label>
        <input class="form-control {{ $errors->has('value') ? 'is-invalid' : '' }}" type="number" name="value" id="value" value="{{ old('value') }}" step="any" required>
        @if($errors->has('value'))
            <span class="text-danger">{{ $errors->first('value') }}</span>
        @endif
    </div>
	<x-enum key='scope' :model='app(\App\Models\Discount::class)'></x-enum>

    <div wire:ignore class="form-group" id="product_brand_div">
		<label class="" id="product_brand_label" for="product_brand">{{ trans('cruds.discount.fields.product_brand') }}</label><span class="help-block"> (Select company first)</span>
		<select class="form-control {{ $errors->has('product_brand_id') ? 'is-invalid' : '' }}" name="product_brand_id" id="product_brand" disabled>
		</select>
		@if($errors->has('product_brand_id'))
		<span class="text-danger">{{ $errors->first('product_brand_id') }}</span>
		@endif
		<span class="help-block">{{ trans('cruds.discount.fields.product_brand_helper') }}</span>
	</div>

	<div wire:ignore class="form-group" id="select_product_unit_ids" style="display: none">
		<label>{{ trans('global.product_units') }}</label>
		<select name="product_unit_ids[]" class="form-control w-100" id="selectProduct" multiple style="width: 100%"></select>
		@if($errors->has('product_unit_ids'))
		<span class="text-danger">{{ $errors->first('product_unit_ids') }}</span>
		@endif
		<span class="help-block">{{ trans('cruds.discount.fields.discount_by_product_units_helper') }}</span>
	</div>

	<div wire:ignore class="form-group" id="product_category_div" style="display: none">
		<label class="" id="product_category_label" for="product_category">{{ trans('cruds.discount.fields.product_category') }}</label>
		<select class="form-control {{ $errors->has('product_category') ? 'is-invalid' : '' }}"
			name="product_category" id="product_category" style="width: 100%">
			<option value="">-- Select Product Unit Category --</option>
			@foreach(App\Enums\ProductCategory::getInstances() as $productCategory)
				<option value="{{ $productCategory->value }}" {{ $productCategory == old('product_category') ? 'selected' : '' }}>{{ $productCategory->description }}</option>
			@endforeach
		</select>
		@if($errors->has('product_category'))
		<span class="text-danger">{{ $errors->first('product_category') }}</span>
		@endif
		<span class="help-block">{{ trans('cruds.discount.fields.product_category_helper') }}</span>
	</div>

	<x-input key='start_time' :model='app(\App\Models\Discount::class)' type="datetime"></x-input>
	<x-input key='end_time' :model='app(\App\Models\Discount::class)' type="datetime"></x-input>
	<div class="form-group">
		<div class="form-check {{ $errors->has('is_active') ? 'is-invalid' : '' }}">
			<input type="hidden" name="is_active" value="0">
			<input class="form-check-input" type="checkbox" name="is_active" id="is_active"
			value="1" {{ old('is_active', 0) == 1 || old('is_active') === null ? 'checked' : '' }}>
			<label class="form-check-label"
			for="is_active">{{ trans('cruds.discount.fields.is_active') }}</label>
		</div>
		@if($errors->has('is_active'))
		<span class="text-danger">{{ $errors->first('is_active') }}</span>
		@endif
		<span class="help-block">{{ trans('cruds.discount.fields.is_active_helper') }}</span>
	</div>
	<x-input key='max_discount_price_per_order' :model='app(\App\Models\Discount::class)' type="number"
	required="0"></x-input>
	<x-input key='max_use_per_customer' :model='app(\App\Models\Discount::class)' type="number"
	required="0"></x-input>
	<x-input key='min_order_price' :model='app(\App\Models\Discount::class)' type="number"
	required="0"></x-input>

	<div class="form-group">
		<label class="" for="promo_id">{{ trans('cruds.payment.fields.promo') }}</label>
		<select class="form-control select2 {{ $errors->has('promo_id') ? 'is-invalid' : '' }}"
			name="promo_id" id="promo_id" required>

			<option value="">-- none --</option>
			@if ($company_id)
			@forelse($promos as $promo)
			<option value="{{ $promo->id }}" {{ old('promo_id') == $promo->id ? 'selected' : '' }} >{{ $promo->name }}</option>
			@empty
			<option value="">-- no promo available --</option>
			@endforelse
			@endif
		</select>
		@if($errors->has('promo_id'))
		<span class="text-danger">{{ $errors->first('promo_id') }}</span>
		@endif
		<span class="help-block">{{ trans('cruds.payment.fields.promo_helper') }}</span>
	</div>
</div>
