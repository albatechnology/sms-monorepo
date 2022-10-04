<?php

namespace App\Http\Requests;

use App\Models\SupervisorType;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroySupervisorTypeRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('supervisor_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:supervisor_types,id',
        ];
    }
}
