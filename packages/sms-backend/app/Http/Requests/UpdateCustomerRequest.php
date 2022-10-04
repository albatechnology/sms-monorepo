<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('customer_edit');
    }

    public function rules()
    {
        return [
            'title'      => [
                'required',
            ],
            'first_name'      => [
                'string',
                'required',
            ],
            'last_name'       => [
                'string',
                'nullable',
            ],
            'email'           => [
                'string',
                'required',
            ],
            'phone'           => [
                'string',
                'required',
            ],
            'default_address_id' => [
                'nullable',
                Rule::exists('addresses', 'id')
                    ->where('customer_id', request()->route('customer')->id),
            ],
            'description' => [
                'nullable',
            ],
        ];
    }
}
