<?php

namespace App\Http\Requests;

use App\Models\Product;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateProductRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('product_edit');
    }

    public function rules()
    {
        return [
            // 'company_id'   => [
            //     'required',
            //     'integer',
            // ],
            'product_brand_id'   => [
                'required',
                'exists:product_brands,id',
            ],
            'product_category_id'   => [
                'required',
                'exists:product_categories,id',
            ],
            'name'         => [
                'string',
                'required',
            ],
            // 'tags.*'       => [
            //     'integer',
            // ],
            // 'tags'         => [
            //     'array',
            // ],
            'price'     => [
                'required',
                'integer',
            ],
            'is_active'     => [
                'nullable',
                'integer',
            ],
            'description' => [
                'nullable',
            ],
        ];
    }
}
