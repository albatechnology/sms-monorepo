<?php

namespace App\Http\Resources\V1\Product;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ProductBrandResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("estimated_value", Schema::TYPE_INTEGER, 1, value: function($data){
                return $data?->pivot?->estimated_value ?? 0;
            }),
            ResourceData::make("order_value", Schema::TYPE_INTEGER, 1, value: function($data){
                return $data?->pivot?->order_value ?? 0;
            }),
            ...BaseProductBrandResource::data(),
            ResourceData::images(),
        ];
    }
}
