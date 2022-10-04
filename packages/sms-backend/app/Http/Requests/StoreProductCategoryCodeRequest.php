<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\HasCompanyAccess;

class StoreProductCategoryCodeRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('product_category_code_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'company_id'  => 'required', new HasCompanyAccess,
        ];
    }
}
