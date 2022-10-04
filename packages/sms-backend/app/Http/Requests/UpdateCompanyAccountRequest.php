<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyAccountRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('company_account_edit');
    }

    public function rules()
    {
        return [
            'name'           => [
                'string',
                'required',
            ],
            'bank_name'      => [
                'string',
            ],
            'account_name'   => [
                'string',
            ],
            'account_number' => [
                'string',
            ],
        ];
    }
}
