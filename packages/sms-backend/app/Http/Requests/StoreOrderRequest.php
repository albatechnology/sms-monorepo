<?php

namespace App\Http\Requests;

use App\Models\Address;
use App\Models\Lead;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('order_create');
    }

    public function rules()
    {
        $lead     = $this->input('lead_id') ? Lead::with('customer')->where('id', $this->input('lead_id'))->first() : null;
        $customer = $lead ? $lead->customer : null;

        return [
            'user_id' => 'required|integer',
            'products' => 'required|array',
            'qty' => 'required|array',
            'discount_id' => ['nullable', 'integer', 'exists:discounts,id'],
            'discount_ids' => ['nullable', 'array'],
            'discount_ids.*' => 'exists:discounts,id',
            'interior_design_id' => 'nullable|integer',
            'expected_price' => 'nullable|integer',
            // 'shipping_address_id' => [
            //     'required',
            //     function ($attribute, $value, $fail) use ($customer) {
            //         if (!$customer) return null;

            //         $address = Address::where('customer_id', $customer->id ?? 0)
            //             ->where('id', $value)
            //             ->get();

            //         if ($address->isEmpty()) $fail('Invalid shipping address selected.');
            //     },
            // ],
            // 'billing_address_id' => [
            //     'required',
            //     function ($attribute, $value, $fail) use ($customer) {
            //         if (!$customer) return null;

            //         $address = Address::where('customer_id', $customer->id ?? 0)
            //             ->where('id', $value)
            //             ->get();

            //         if ($address->isEmpty()) $fail('Invalid billing address selected.');
            //     },
            // ],
            'tax_invoice_id' => [
                'nullable',
                Rule::unique('tax_invoices', 'id')->where('customer_id', $customer->id ?? 0)
            ],
            'lead_id' => [
                'required',
                // function ($attribute, $value, $fail) {
                //     // currently only allow the sales that owns the lead to select the lead
                //     if (!user()->leads()->where('id', $value)->first()) $fail('The selected lead does not belong to this user.');
                // },
            ],
            'note' => 'nullable|string',
            'packing_fee' => 'nullable|integer|min:0',
            'shipping_fee' => 'nullable|integer|min:0',
            'discount_type' => 'nullable|integer|min:0',
            'additional_discount' => 'nullable|integer|min:0',
            'expected_shipping_datetime' => 'required|string|date|after:yesterday',
            'quotation_valid_until_datetime' => 'nullable|string|date|after:yesterday',
        ];
    }
}
