<?php

namespace App\Http\Requests;

use App\Enums\ProductCategory;
use BenSampo\Enum\Rules\EnumValue;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductUnitRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('product_unit_edit');
    }

    public function rules()
    {
        return [
            'colour_id'   => [
                'required',
                'exists:colours,id',
            ],
            'covering_id' => [
                'required',
                'exists:coverings,id',
            ],
            'name'        => [
                'string',
                'required',
            ],
            'description' => [
                'string',
                'nullable',
            ],
            'sku'         => [
                'string',
                'required',
            ],
            'price'       => [
                'integer',
                'required',
                'min:0',
            ],
            'volume' => 'nullable|min:0',
            'purchase_price' => 'required|integer|min:1',
            'production_cost' => 'nullable|integer|min:0',
            'is_active'   => [
                'boolean',
            ],
            'product_unit_category'        => [
                'nullable',
                new EnumValue(ProductCategory::class, 0)
            ],
        ];
    }
}
