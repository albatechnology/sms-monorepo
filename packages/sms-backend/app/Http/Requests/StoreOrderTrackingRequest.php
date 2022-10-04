<?php

namespace App\Http\Requests;

use App\Models\OrderTracking;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreOrderTrackingRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('order_tracking_create');
    }

    public function rules()
    {
        return [];
    }
}
