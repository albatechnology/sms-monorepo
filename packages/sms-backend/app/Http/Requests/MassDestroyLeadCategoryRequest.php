<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyLeadCategoryRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('payment_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
        ];
    }
}
