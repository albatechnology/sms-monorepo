<?php

namespace App\Http\Requests;

use App\Enums\ProductCategory;
use App\Enums\DiscountScope;
use App\Enums\DiscountType;
use App\Models\Promo;
// use App\Rules\HasCompanyAccess;
use BenSampo\Enum\Rules\EnumValue;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDiscountRequest extends FormRequest
{
	public function authorize()
	{
		return Gate::allows('discount_create');
	}

	public function rules()
	{
        // $companyId = $this->get('company_id');

		return [
			'name'                         => [
				'string',
				'required',
			],
			'description'                  => [
				'string',
				'nullable',
			],
			'type'                         => [
				'required',
				new EnumValue(DiscountType::class, 0)
			],
			'activation_code'              => [
				'string',
				'min:4',
				'nullable',
			],
			'value'                        => [
				'required',
				'numeric',
				'min:0',
				function ($attribute, $value, $fail) {
					$type = $this->get('type');

					if (!isset($type)) {
						return;
					}

					$type = DiscountType::fromValue((int)$type);

                    // if percentage type, max to 100%
					if ($value > 100 && $type->is(DiscountType::PERCENTAGE)) {
						$fail('Value cant be greater than 100%');
					}
				}
			],
			'scope'                        => [
				'required',
				new EnumValue(DiscountScope::class, 0)
			],
			'product_category'        => [
				'nullable',
				new EnumValue(ProductCategory::class, 0)
			],
			'product_brand_id'        => [
				'nullable',
				Rule::exists('product_brands', 'id')
					// ->where('company_id', $companyId)
			],
			// 'start_time'                   => [
			// 	'required',
			// 	'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
			// ],
			// 'end_time'                     => [
			// 	'required',
			// 	'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
			// 	'after:start_time'
			// ],
			'max_use_per_customer'         => [
				'nullable',
				'integer',
				'min:0',
				'max:2147483647',
			],
			'is_active'                    => [
				'boolean'
			],
			'min_order_price'              => [
				'nullable',
				'integer',
				'min:0',
			],
			'max_discount_price_per_order' => [
				'nullable',
				'integer',
				'min:0',
			],
			// 'company_id'                   => [
			// 	'required',
			// 	new HasCompanyAccess(),
			// ],
			'product_ids'             => [
				'nullable',
				'array',
			],
			'promo_id'                   => [
                'required', 'exists:promos,id'
				// 'nullable',
				// function($attribute, $value, $fail){
				// 	$promo = Promo::query()
				// 	->where('id', $value)
				// 	->doesntHave('discount')
				// 	->exists();

				// 	if(!$promo){
				// 		$fail('Invalid promo');
				// 	}

				// 	return;
				// }
			],
		];
	}
}
