<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('product_category_create');
    }

    public function rules()
    {
        return [
            'name'       => [
                'string',
                'required',
            ],
            'description'       => [
                'string',
                'nullable',
            ],
            // 'company_id' => [
            //     'required',
            //     'integer',
            // ],
        ];
    }
}
