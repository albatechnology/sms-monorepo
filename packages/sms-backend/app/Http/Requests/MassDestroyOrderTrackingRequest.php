<?php

namespace App\Http\Requests;

use App\Models\OrderTracking;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyOrderTrackingRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('order_tracking_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:order_trackings,id',
        ];
    }
}
