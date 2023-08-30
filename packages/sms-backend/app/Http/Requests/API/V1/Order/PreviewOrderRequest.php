<?php

namespace App\Http\Requests\API\V1\Order;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use App\Enums\LeadType;
use App\Models\Address;
use App\Models\Lead;
use App\Models\Order;
use App\Models\Product;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Illuminate\Validation\Rule;

class PreviewOrderRequest extends BaseApiRequest
{
    protected ?string $model = Order::class;

    public static function getSchemas(): array
    {
        return [
            Schema::array('items')->items(
                Schema::object()->properties(
                    Schema::integer('id')->example(1)->description('The product unit id to add to cart'),
                    Schema::integer('quantity')->example(1),
                ),
            ),
            // Schema::array('voucher_ids')->example(['GratisOngkir', 'Clearance', 'CuciGudang']),
            // Schema::array('discount_ids')->example([1, 2, 3]),
            Schema::integer('discount_id')->example(1),
            Schema::integer('expected_price')
                ->example(1000)
                ->description('Provide expected price of the order for consistency checking.')
                ->nullable(),
            Schema::integer('shipping_address_id')->example(1),
            Schema::integer('billing_address_id')->example(1),
            Schema::integer('tax_invoice_id')->example(1),
            Schema::integer('lead_id')->example(1),
            Schema::string('note')->example('Note placed on order'),
            Schema::integer('shipping_fee')->example(10000),
            Schema::integer('packing_fee')->example(10000),
            Schema::integer('discount_type')->example(1),
            Schema::integer('additional_discount')->example(10000),
            Schema::string('expected_shipping_datetime')->example(ApiDataExample::TIMESTAMP),
            Schema::string('quotation_valid_until_datetime')->example(ApiDataExample::TIMESTAMP),
        ];
    }

    protected static function data()
    {
        return [];
    }

    public function rules(): array
    {
        $lead     = $this->input('lead_id') ? Lead::with('customer')->where('id', $this->input('lead_id'))->first() : null;
        $customer = $lead ? $lead->customer : null;

        return [
            'items'            => 'nullable|array',
            'items.*.is_ready' => 'nullable|boolean',
            'items.*.id'       => [
                Rule::requiredIf(!empty(request()->input('items'))),
                function ($attribute, $value, $fail) {
                    $unit = Product::whereActive()->where('id', $value)->first();
                    if (!$unit) {
                        $fail('Invalid or inactive product unit.');
                        return;
                    }

                    // if (is_null($unit->colour_id)) {
                    //     $unit->update(['is_active' => 0]);
                    //     $fail("Product unit {$unit->id} does not have colour");
                    // }

                    // if (is_null($unit->covering_id)) {
                    //     $unit->update(['is_active' => 0]);
                    //     $fail("Product unit {$unit->id} does not have covering");
                    // }
                }
            ],
            'items.*.quantity' => [Rule::requiredIf(!empty(request()->input('items'))), 'integer', 'min:1'],
            // 'voucher_ids' => 'nullable|array',
            // 'voucher_ids.*' => 'string|exists:vouchers,id',
            'discount_id' => 'nullable|exists:discounts,id',
            // 'discount_ids' => 'nullable|array',
            // 'discount_ids.*' => 'integer|exists:discounts,id',
            'expected_price'      => 'nullable|integer',
            'shipping_address_id' => [
                'required',
                function ($attribute, $value, $fail) use ($customer) {
                    if (!$customer) return null;

                    $address = Address::where('customer_id', $customer->id ?? 0)
                        ->where('id', $value)
                        ->get();

                    if ($address->isEmpty()) $fail('Invalid shipping address selected.');
                },
            ],
            'billing_address_id' => [
                'required',
                function ($attribute, $value, $fail) use ($customer) {
                    if (!$customer) return null;

                    $address = Address::where('customer_id', $customer->id ?? 0)
                        ->where('id', $value)
                        ->get();

                    if ($address->isEmpty()) $fail('Invalid billing address selected.');
                },
            ],
            'tax_invoice_id' => [
                'nullable',
                Rule::unique('tax_invoices', 'id')->where('customer_id', $customer->id ?? 0)
            ],

            'lead_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    // currently only allow the sales that owns the lead to select the lead
                    $lead = user()->leads()->where('id', $value)->first();
                    if (!$lead) $fail('The selected lead does not belong to this user.');
                    if ($lead?->type->in([LeadType::DEAL, LeadType::DROP])) $fail('This lead is already a deal and can not be ordered again');
                },
            ],
            'packing_fee'  => 'nullable|integer|min:0',
            'shipping_fee' => 'nullable|integer|min:0',
            'discount_type' => 'nullable|integer|min:0',
            'additional_discount' => 'nullable|integer|min:0',
            'note'                           => 'nullable|string',
            'expected_shipping_datetime'     => 'required|string|date',
            'quotation_valid_until_datetime' => 'nullable|string|date',
        ];
    }

    public function messages()
    {
        return [
            'shipping_address_id.required' => 'Shipping address is required',
            'billing_address_id.required' => 'Billing address is required',
            'expected_shipping_datetime.required' => 'Expected delivery date is required',
        ];
    }
}
