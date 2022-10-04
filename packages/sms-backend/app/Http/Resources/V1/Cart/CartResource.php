<?php

namespace App\Http\Resources\V1\Cart;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class CartResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::makeRelationshipCollection('items', CartItemLineResource::class, null, fn($q) => collect($q['items'])),
            //ResourceData::make('discount_id', Schema::TYPE_INTEGER, 1)->nullable(),
            ResourceData::make('total_price', Schema::TYPE_INTEGER, 1000),
            //ResourceData::make('total_discount', Schema::TYPE_INTEGER, 100),
            //ResourceData::make('customer_id', Schema::TYPE_INTEGER, 1)->nullable(),
            //ResourceData::makeEnum('discount_error', DiscountError::class, true),
        ];
    }
}