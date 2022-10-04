<?php

namespace App\Http\Requests;

use App\Models\Target;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreNewTargetRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('target_create');
    }

    public function rules()
    {
        return [
            'start_date'   => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'end_date'     => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'name'         => [
                'string',
                'required',
            ],
            'value'        => [
                'required',
            ],
            'type'         => [
                'required',
            ],
        ];
    }
}
