<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Gate;

class UpdateInteriorDesignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('interior_design_edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'bum_id'                => [
                'integer',
                'nullable',
            ],
            'sales_id'              => [
                'integer',
                'nullable',
            ],
            'religion_id'           => [
                'integer',
                'nullable',
            ],
            'name'                  => [
                'string',
                'required',
            ],
            'company'               => [
                'string',
                'nullable',
            ],
            'owner'                 => [
                'string',
                'nullable',
            ],
            'npwp'                  => [
                'string',
                'nullable',
            ],
            'address'               => [
                'string',
                'nullable',
            ],
            'phone'                 => [
                'string',
                'nullable',
            ],
            'email'                 => [
                'string',
                'nullable',
            ],
            'bank_account_name'     => [
                'string',
                'nullable',
            ],
            'bank_account_holder'   => [
                'string',
                'nullable',
            ],
            'bank_account_number'   => [
                'string',
                'nullable',
            ],
        ];
    }
}
