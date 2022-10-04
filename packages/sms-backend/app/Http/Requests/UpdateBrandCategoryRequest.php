<?php

namespace App\Http\Requests;

use App\Models\BrandCategory;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateBrandCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('brand_category_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'code' => [
                'string',
                'nullable',
            ],
        ];
    }
}
