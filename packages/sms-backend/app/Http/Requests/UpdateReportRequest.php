<?php

namespace App\Http\Requests;

use App\Enums\ReportableType;
use BenSampo\Enum\Rules\EnumValue;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReportRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('report_edit');
    }

    public function rules()
    {
        return [
            'name'            => [
                'string',
                'nullable',
            ],
            'start_date'      => [
                'date',
                'required',
            ],
            'end_date'        => [
                'date',
                'required',
            ],
//            'start_time'      => [
//                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
//                'nullable',
//            ],
            'reportable_type' => [
                'required',
                new EnumValue(ReportableType::class)
            ],
            'reportable_id'   => [
                'required',
                'integer',
            ],
        ];
    }
}
