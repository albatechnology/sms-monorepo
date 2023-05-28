<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductBrandRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('product_brand_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            // 'company_id' => [
            //     'required',
            // ],
            // 'hpp_calculation' => [
            //     'required',
            //     'integer',
            // ],
            // 'currency_id' => [
            //     'required',
            //     'integer',
            // ],
            'brand_category_id' => [
                'required',
                'exists:brand_categories,id',
            ],
        ];
    }
}
