<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductBrandRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('product_brand_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            // 'hpp_calculation' => [
            //     'integer',
            //     'required',
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
