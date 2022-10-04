<?php

namespace App\Http\Resources\V1\Discount;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use App\Enums\DiscountScope;
use App\Enums\DiscountType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class BaseDiscountResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make('name', Schema::TYPE_STRING, 'Discount Name'),
            ResourceData::make('activation_code', Schema::TYPE_STRING, 'Activation Code'),
            ResourceData::make('description', Schema::TYPE_STRING, 'Discount Example')->nullable(),
            ResourceData::makeEnum('type', DiscountType::class),
            ResourceData::makeEnum('scope', DiscountScope::class),
            ResourceData::make('value', Schema::TYPE_INTEGER, 50),
            ResourceData::make('max_discount_price_per_order', Schema::TYPE_INTEGER, 1)->nullable(),
            ResourceData::make('max_use_per_customer', Schema::TYPE_INTEGER, 1)->nullable(),
            ResourceData::make('min_order_price', Schema::TYPE_INTEGER, 1)->nullable(),
            // ResourceData::make('product_unit_ids', Schema::TYPE_ARRAY)->nullable(),
        ];
    }
}
