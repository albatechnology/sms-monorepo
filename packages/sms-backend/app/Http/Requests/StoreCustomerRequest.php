<?php

namespace App\Http\Requests;

use App\Models\Customer;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreCustomerRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('customer_create');
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
                'unique:customers,email',
            ],
            'phone'           => [
                'string',
                'required',
                'unique:customers,phone',
            ],
            'default_address_id' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'description' => [
                'nullable',
            ],
        ];
    }
}
