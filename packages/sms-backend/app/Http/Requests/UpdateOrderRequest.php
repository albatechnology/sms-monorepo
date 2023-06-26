<?php

namespace App\Http\Requests;

use App\Enums\OrderStatus;
use BenSampo\Enum\Rules\EnumValue;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('order_edit');
    }

    public function rules()
    {
        return [
            'deal_at' => ['nullable', 'date_format:Y-m-d H:i:s'],
            'note'       => [],
            'channel_id' => ['required', 'integer'],
            'expected_shipping_datetime' => ['required', 'date_format:Y-m-d H:i:s'],
            'created_at' => ['required', 'date_format:Y-m-d H:i:s'],
            //            'stock_status' => [
            //                'required',
            //                'integer',
            //            ],
            //            'payment_status' => [
            //                'required',
            //                'integer',
            //            ],
            'status'     => [ 'required', new EnumValue(OrderStatus::class, false)],
            //            'shipment_status' => [
            //                'required',
            //                'integer',
            //            ],
        ];
    }
}
