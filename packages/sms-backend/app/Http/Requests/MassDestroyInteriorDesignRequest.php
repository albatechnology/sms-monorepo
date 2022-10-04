<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MassDestroyInteriorDesignRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('interior_design_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:interior_designs,id',
        ];
    }
}
