<?php

namespace App\Http\Resources\V1\CartDemand;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class CartDemandResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make('id', Schema::TYPE_INTEGER, 1)->nullable(),
            ResourceData::makeRelationshipCollection('items', CartDemandItemLineResource::class, null, fn($q) => isset($q['items']) && count($q['items']) > 0 ? $q['items'] : []),
            ResourceData::make('total_price', Schema::TYPE_INTEGER, 1000),
            //ResourceData::make('total_discount', Schema::TYPE_INTEGER, 100),
            //ResourceData::make('customer_id', Schema::TYPE_INTEGER, 1)->nullable(),
            //ResourceData::makeEnum('discount_error', DiscountError::class, true),
        ];
    }
}
