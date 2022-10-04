<?php

namespace App\Http\Requests;

use App\Enums\ShipmentStatus;
use App\Models\Order;
use App\Models\OrderDetail;
use BenSampo\Enum\Rules\EnumValue;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class StoreShipmentRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('shipment_create');
    }

    public function rules()
    {
        return [
            'order_id'          => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $order = Order::query()
                        ->tenanted()
                        //->whereWaitingDelivery()
                        ->where('id', $value)
                        ->first();

                    if (!$order) {
                        $fail('Invalid order!');
                    }
                }
            ],
            'detail'            => 'array|required',
            'detail.*.quantity' => 'required|integer',
            'detail.*.id'       => 'required',
            'detail.*'          => [
                function ($attribute, $value, $fail) {

                    if (!isset($value['quantity'], $value['id'])) {
                        $fail('Internal error! Please notify developer.');
                        \Log::error("Bad form name on StoreShipmentRequest");
                    }

                    $detail = OrderDetail::query()
                        ->where('id', $value['id'])
                        ->where('order_id', request()->get('order_id') ?? 0)
                        ->first();

                    if (!$detail) {
                        $fail('Invalid order detail!');
                    }

                    if ($value['quantity'] > $detail->quantity) {
                        $fail('Invalid handle quantity for order detail ID ' . $detail->id);
                    }
                }
            ],
            'status'            => [
                'required',
                new EnumValue(ShipmentStatus::class, 0)
            ],
            'reference'         => [
                'string',
                'nullable',
            ],
            'note'              => [
                'string',
                'nullable',
            ],
        ];
    }
}
