<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCoveringRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('covering_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'type' => [
                'string',
                'nullable',
            ],
        ];
    }
}
