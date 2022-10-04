<?php

namespace App\Http\Requests;

use App\Models\Activity;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreActivityRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('activity_create');
    }

    public function rules()
    {
        return [
            'user_id'            => [
                'required',
                'integer',
            ],
            'lead_id'            => [
                'required',
                'integer',
            ],
            'products.*'         => [
                'integer',
            ],
            'products'           => [
                'array',
            ],
            'follow_up_datetime' => [
                'required',
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
            ],
        ];
    }
}
