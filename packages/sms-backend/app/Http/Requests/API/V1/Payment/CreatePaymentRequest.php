<?php

namespace App\Http\Requests\API\V1\Payment;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\RequestData;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Payment;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class CreatePaymentRequest extends BaseApiRequest
{
    protected ?string $model = Payment::class;

    public static function data(): array
    {
        $orderRule = [
            'required',
            function ($attribute, $value, $fail) {
                $order = Order::find($value);

                if (!$order) {
                    $fail('Order not found');
                }

                if ($order->status->in([OrderStatus::CANCELLED, OrderStatus::RETURNED])) {
                    $fail('Cannot make payment to cancelled or returned order');
                }
            }
        ];

        return [
            RequestData::make('amount', Schema::TYPE_INTEGER, 1, 'required|integer'),
            RequestData::make('reference', Schema::TYPE_STRING, 'My Payment', 'nullable|min:1|max:100'),
            RequestData::make('payment_type_id', Schema::TYPE_INTEGER, 1, 'required|exists:payment_types,id'),
            RequestData::make('order_id', Schema::TYPE_INTEGER, 1, $orderRule, 'required|exists:orders,id'),
        ];
    }

    public function authorize()
    {
        return true;
    }
}