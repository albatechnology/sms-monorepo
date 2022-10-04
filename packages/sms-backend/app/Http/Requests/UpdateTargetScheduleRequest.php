<?php

namespace App\Http\Requests;

use App\Models\TargetSchedule;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateTargetScheduleRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('target_schedule_edit');
    }

    public function rules()
    {
        return [
            'duration_type'       => [
                'required',
            ],
            'start_date'          => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'target_name'         => [
                'string',
                'nullable',
            ],
            'value'               => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'target_value_type'   => [
                'required',
            ],
            'target_type'         => [
                'required',
            ],
            'target_subject'      => [
                'required',
            ],
            'target_subject_type' => [
                'required',
            ],
        ];
    }
}
