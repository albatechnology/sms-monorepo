<?php

namespace App\Http\Resources\V1\OrderDiscount;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use App\Http\Resources\V1\Discount\DiscountResource;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class OrderDiscountResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make('order_id', Schema::TYPE_INTEGER, 1),
            ResourceData::makeRelationship('discount', DiscountResource::class),
        ];
    }
}
