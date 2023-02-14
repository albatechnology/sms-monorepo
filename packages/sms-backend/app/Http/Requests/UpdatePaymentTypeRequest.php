<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentTypeRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('payment_type_edit');
    }

    public function rules()
    {
        return [
            'name'                => [
                'string',
                'required',
            ],
            'payment_category_id' => [
                'required',
                'exists:payment_categories,id',
            ]
        ];
    }
}
