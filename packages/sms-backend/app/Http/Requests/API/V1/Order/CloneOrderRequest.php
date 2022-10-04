<?php

namespace App\Http\Requests\API\V1\Order;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use App\Models\Order;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class CloneOrderRequest extends BaseApiRequest
{
    protected ?string $model = Order::class;

    public static function getSchemas(): array
    {
        return [
            Schema::integer('expected_price')
                ->example(1000)
                ->description('Provide expected price of the order for consistency checking.')
                ->nullable(),
            Schema::string('note')->example('Note placed on order'),
            Schema::integer('additional_discount')->example(10000),
            Schema::string('expected_shipping_datetime')->example(ApiDataExample::TIMESTAMP),
        ];
    }

    protected static function data()
    {
        return [];
    }

    public function rules(): array
    {
        return [
            'expected_price'             => 'nullable|integer',
            'note'                       => 'nullable|string',
            'additional_discount'        => 'nullable|integer|min:0',
            'expected_shipping_datetime' => 'nullable|string|date|after:now',
        ];
    }
}