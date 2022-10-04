<?php

namespace App\Http\Requests;

use App\Models\PaymentCategory;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

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
            'company_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
