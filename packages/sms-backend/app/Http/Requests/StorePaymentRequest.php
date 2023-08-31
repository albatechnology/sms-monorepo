<?php

namespace App\Http\Requests;

use App\Enums\PaymentStatus;
use App\Models\Order;
use BenSampo\Enum\Rules\EnumValue;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('payment_create');
    }

    public function rules()
    {
        $invoiceNumber = request()->get('invoice_number');
        $order         = Order::tenanted()
            ->where('invoice_number', $invoiceNumber)
            ->first();

        $invoiceRule = [
            'invoice_number' => [
                'required',
                function ($attribute, $value, $fail) use ($order) {
                    if (!$order) {
                        $fail('Invalid invoice number!');
                    }
                }
            ]
        ];

        if (!$order) {
            return $invoiceRule;
        }

        return [
            'amount'          => [
                'required', 'integer'
            ],
            'payment_type_id' => [
                'required',
                Rule::exists('payment_types', 'id')->where('subscribtion_user_id', $order->subscribtion_user_id),
            ],
            'reference'       => [
                'string',
                'nullable',
            ],
            'status'          => [
                'required',
                new EnumValue(PaymentStatus::class, 0)
            ],
            'reason'          => [
                'string',
                'nullable',
            ],
            'invoice_number'  => $invoiceRule['invoice_number']
        ];
    }
}
