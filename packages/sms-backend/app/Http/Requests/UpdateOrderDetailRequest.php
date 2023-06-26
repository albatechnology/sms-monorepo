<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderDetailRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('order_detail_edit');
    }

    public function rules()
    {
        return [
            'status' => [
                'required',
                'integer',
            ],
        ];
    }
}
