<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTargetRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('target_edit');
    }

    public function rules()
    {
        return [
            'target' => [
                'required',
                'integer',
                'min:0'
            ],
        ];
    }
}
