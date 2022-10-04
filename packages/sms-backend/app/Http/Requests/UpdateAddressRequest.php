<?php

namespace App\Http\Requests;

use App\Models\Address;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateAddressRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('address_edit');
    }

    public function rules()
    {
        return [
            'address_line_1' => [
                'string',
                'required',
            ],
            'address_line_2' => [
                'string',
                'nullable',
            ],
            'address_line_3' => [
                'string',
                'nullable',
            ],
            'city'           => [
                'string',
                'nullable',
            ],
            'country'        => [
                'string',
                'nullable',
            ],
            'province'       => [
                'string',
                'nullable',
            ],
            'type'           => [
                'required',
            ],
            'phone'          => [
                'string',
                'nullable',
            ],
            'customer_id' => [
                'integer',
                'required',
            ],
        ];
    }
}
