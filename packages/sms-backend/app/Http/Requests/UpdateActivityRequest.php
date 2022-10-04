<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateActivityRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('activity_edit');
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
            'customer_id'        => [
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
            'feedback' => [],
            'created_at' => [],
        ];
    }
}
