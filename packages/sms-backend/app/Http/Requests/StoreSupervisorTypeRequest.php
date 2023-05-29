<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class StoreSupervisorTypeRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('supervisor_type_create');
    }

    public function rules()
    {
        return [
            'name'  => [
                'string',
                'required',
            ],
            'level' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'discount_approval_limit_percentage' => [
                'required',
                'integer',
                'min:0',
                'max:100',
            ],
            // 'discount_approval_limit_percentage.*' => [
            //     'required',
            //     'integer',
            //     'min:0',
            //     'max:100',
            // ],
            // 'can_assign_lead' => [
            //     'nullable',
            //     'boolean'
            // ]
        ];
    }
}
