<?php

namespace App\Http\Requests;

use App\Rules\HasCompanyAccess;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePromoCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('promo_edit');
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
                'nullable',
                new HasCompanyAccess(),
            ],
        ];
    }
}
