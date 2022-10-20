<?php

namespace App\Http\Requests;

use App\Enums\ProductCategory;
use App\Rules\HasCompanyAccess;
use BenSampo\Enum\Rules\EnumValue;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductUnitRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('product_unit_create');
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        $productId = (int)($this->get('product_id') ?? 0);
        $companyId = (int)($this->get('company_id') ?? 0);

        return [
            'name'            => 'required|min:1',
            'sku'             => 'required|min:1',
            'description'     => 'nullable|min:1',
            'price'           => 'required|integer|min:1',
            'volume'     => 'nullable|min:0',
            'purchase_price' => 'required|integer|min:1',
            'production_cost' => 'nullable|integer|min:0',
            'is_active'       => 'nullable|boolean',
            'company_id'      => 'required', new HasCompanyAccess,
            'product_id'      => [
                'required',
                Rule::exists('products', 'id')
                    ->where('company_id', $companyId)
            ],
            'colour_id'       => [
                'required',
                Rule::exists('colours', 'id')
                    ->where('product_id', $productId)
                    ->where('company_id', $companyId)
            ],
            'covering_id'     => [
                'required',
                Rule::exists('coverings', 'id')
                    ->where('product_id', $productId)
                    ->where('company_id', $companyId)
            ],
            'product_unit_category'        => [
                'nullable',
                new EnumValue(ProductCategory::class, 0)
            ],
        ];
    }
}
