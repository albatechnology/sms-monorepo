<?php

namespace App\Http\Requests;

use App\Models\OrderDetail;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyOrderDetailRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('order_detail_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:order_details,id',
        ];
    }
}
