<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class StorePaymentCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('payment_category_create');
    }

    public function rules()
    {
        return [
            'name'       => [
                'string',
                'nullable',
            ],
            // 'company_id' => [
            //     'required',
            //     'integer',
            // ],
        ];
    }
}
