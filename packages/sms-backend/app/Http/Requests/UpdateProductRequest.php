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
            'product_brand_id'   => [
                'required',
                'integer',
            ],
            'name'         => [
                'string',
                'required',
            ],
            'product_model_id'   => [
                'required',
                'integer',
            ],
            'product_version_id'   => [
                'required',
                'integer',
            ],
            'product_category_code_id'   => [
                'required',
                'integer',
            ],
            'categories.*' => [
                'integer',
            ],
            'categories'   => [
                'array',
            ],
            'tags.*'       => [
                'integer',
            ],
            'tags'         => [
                'array',
            ],
            'company_id'   => [
                'required',
                'integer',
            ],
            'channels.*'   => [
                'integer',
            ],
            'channels'     => [
                'array',
            ],
            'video_url' => [
                'string',
                'nullable',
            ],
            'description' => [
                'nullable',
            ],
        ];
    }
}
