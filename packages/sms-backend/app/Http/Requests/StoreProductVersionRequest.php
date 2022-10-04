<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\HasCompanyAccess;

class StoreProductVersionRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('product_version_create');
    }

    public function rules()
    {
        return [
            'name'   => [
                'string',
                'required',
            ],
            'height' => [
                'string',
                'nullable',
            ],
            'length' => [
                'string',
                'nullable',
            ],
            'width'  => [
                'string',
                'nullable',
            ],
            'company_id'  => 'required', new HasCompanyAccess,
        ];
    }
}
