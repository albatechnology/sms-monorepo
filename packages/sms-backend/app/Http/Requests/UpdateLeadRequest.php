<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLeadRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('lead_edit');
    }

    public function rules()
    {
        return [
            'type'       => [
                'required',
            ],
            'status'     => [
                'required',
            ],
            'label'      => [
                'string',
                'nullable',
            ],
            'sales'      => [
                'required',
            ],
            'channel_id' => [
                'required',
                'integer',
            ],
            'interest'      => [
                'string',
                'nullable',
            ],
        ];
    }
}
