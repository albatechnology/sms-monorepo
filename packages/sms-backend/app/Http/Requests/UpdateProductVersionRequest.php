<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductVersionRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('product_version_edit');
    }

    public function rules()
    {
        return [
            'name'   => [
                'string',
                'required',
            ],
            'height' => [
                'string',
                'nullable',
            ],
            'length' => [
                'string',
                'nullable',
            ],
            'width'  => [
                'string',
                'nullable',
            ],
        ];
    }
}
