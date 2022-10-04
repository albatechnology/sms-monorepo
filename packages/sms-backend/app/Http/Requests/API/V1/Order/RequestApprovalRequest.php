<?php

namespace App\Http\Requests\API\V1\Order;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Models\Order;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class RequestApprovalRequest extends BaseApiRequest
{
    protected ?string $model = Order::class;

    public static function getSchemas(): array
    {
        return [
            Schema::integer('discount_type')->example(1),
            Schema::integer('additional_discount')->example(10000),
            Schema::integer('discount_take_over_by')->example(1),
            Schema::integer('approval_note')->example('Turunin diskon ahhhh...'),
        ];
    }

    protected static function data()
    {
        return [];
    }

    public function rules(): array
    {
        return [
            'discount_type' => 'nullable|numeric|min:0',
            'additional_discount' => 'nullable|numeric|min:0',
            'discount_take_over_by' => 'nullable|numeric',
            'approval_note' => 'nullable|string',
        ];
    }
}
