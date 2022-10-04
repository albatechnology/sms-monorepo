<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductModelRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('product_model_edit');
    }

    public function rules()
    {
        return [
            'name'        => [
                'string',
                'required',
            ],
            'description' => [
                'string',
                'nullable',
            ],
        ];
    }
}
