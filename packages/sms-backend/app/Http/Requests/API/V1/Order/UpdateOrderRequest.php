<?php

namespace App\Http\Requests\API\V1\Order;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Models\Order;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdateOrderRequest extends BaseApiRequest
{
    protected ?string $model = Order::class;

    public static function getSchemas(): array
    {
        return [
            Schema::integer('discount_id')->example(1),
            // Schema::array('discount_ids')->example([1,2,3]),
            // Schema::array('voucher_ids')->example(['GratisOngkir', 'Clearance', 'CuciGudang']),
            Schema::integer('discount_type')->example(1),
            Schema::integer('additional_discount')->example(10000),
            Schema::integer('discount_take_over_by')->example(1),
            Schema::integer('approval_note')->example('Turunkan diskon sekarang juga'),
        ];
    }

    protected static function data()
    {
        return [];
    }

    public function rules(): array
    {
        return [
            'discount_id' => ['nullable', 'integer', 'exists:discounts,id'],
            // 'voucher_ids' => 'nullable|array',
            // 'voucher_ids.*' => 'string|exists:vouchers,id',
            // 'discount_ids' => 'nullable|array',
            // 'discount_ids.*' => [
            //     'required', 'integer', 'exists:discounts,id',
            // ],
            'discount_type' => 'nullable|integer|min:0',
            'additional_discount' => 'nullable|integer|min:0',
            'discount_take_over_by' => 'nullable|integer',
            'approval_note' => 'nullable|string',
        ];
    }
}
