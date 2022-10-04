<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\HasCompanyAccess;

class StoreProductModelRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('product_model_create');
    }

    public function rules()
    {
        return [
            'name'        => [
                'string',
                'required',
            ],
            'description' => [
                'string',
                'nullable',
            ],
            'company_id'  => 'required', new HasCompanyAccess,
        ];
    }
}
