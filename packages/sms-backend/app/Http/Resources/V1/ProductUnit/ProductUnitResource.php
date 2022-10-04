<?php

namespace App\Http\Resources\V1\ProductUnit;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ProductUnitResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("id", Schema::TYPE_INTEGER, 1),
            ResourceData::make('description', Schema::TYPE_STRING, 'description')->nullable(),
            ResourceData::make('sku', Schema::TYPE_STRING, '100008100019101514'),
            ...BaseProductUnitResource::data()
        ];
    }
}
