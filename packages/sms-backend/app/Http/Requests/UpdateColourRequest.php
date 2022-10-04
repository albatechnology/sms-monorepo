<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateColourRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('colour_edit');
    }

    public function rules()
    {
        return [
            'name'              => [
                'string',
                'required',
            ]
        ];
    }
}
