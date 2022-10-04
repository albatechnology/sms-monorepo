<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('payment_edit');
    }

    public function rules()
    {
        return [
            'amount'          => [
                'required',
            ],
            'payment_type_id' => [
                'required',
                'integer',
            ],
            'reference'       => [
                'string',
                'nullable',
            ],
            'status'          => [
                'required',
            ],
            'reason'          => [
                'nullable',
            ],
            'created_at' => 'nullable'
        ];
    }
}
