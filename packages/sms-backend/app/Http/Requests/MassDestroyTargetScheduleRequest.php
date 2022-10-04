<?php

namespace App\Http\Requests;

use App\Models\TargetSchedule;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyTargetScheduleRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('target_schedule_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:target_schedules,id',
        ];
    }
}
