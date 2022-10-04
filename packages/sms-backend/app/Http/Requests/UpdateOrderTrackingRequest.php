<?php

namespace App\Http\Requests;

use App\Models\OrderTracking;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateOrderTrackingRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('order_tracking_edit');
    }

    public function rules()
    {
        return [];
    }
}
