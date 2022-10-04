<?php

namespace App\Http\Requests;

use App\Rules\HasCompanyAccess;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class StorePromoCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('promo_category_create');
    }

    public function rules()
    {
        return [
            'name'                  => [
                'string',
                'required',
            ],
            'description'           => [
                'string',
                'nullable',
            ],
            'company_id'            => [
                'required',
                new HasCompanyAccess(),
            ],
        ];
    }
}
