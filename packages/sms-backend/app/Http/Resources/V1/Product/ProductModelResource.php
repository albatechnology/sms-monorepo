<?php

namespace App\Http\Resources\V1\Product;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ProductModelResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("id", Schema::TYPE_INTEGER, 1),
            ...BaseProductModelResource::data(),
            ResourceData::make("description", Schema::TYPE_STRING, "Product description")->nullable(),
            ResourceData::make('price_min', Schema::TYPE_INTEGER, 1)->nullable(),
            ResourceData::make('price_max', Schema::TYPE_INTEGER, 1)->nullable(),
            ResourceData::images(),
        ];
    }
}
