<?php

namespace App\Http\Requests;

use App\Rules\HasCompanyAccess;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyAccountRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('company_account_create');
    }

    public function rules()
    {
        return [
            'name'           => [
                'string',
                'required',
            ],
            'bank_name'      => [
                'string', 'nullable'
            ],
            'account_name'   => [
                'string', 'nullable'
            ],
            'account_number' => [
                'nullable'
            ],
            'company_id'     => [
                'required',
                new HasCompanyAccess(),
            ]
        ];
    }
}
