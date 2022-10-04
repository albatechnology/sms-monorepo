<?php

namespace App\Http\Requests;

use App\Models\Banner;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreBannerRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('banner_create');
    }

    public function rules()
    {
        return [
            'bannerable_type' => [
                'string',
                'required',
            ],
            'bannerable'      => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'start_time'      => [
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
                'nullable',
            ],
            'end_time'        => [
                'string',
                'nullable',
            ],
            'company_id'      => [
                'required',
                'integer',
            ],
        ];
    }
}
