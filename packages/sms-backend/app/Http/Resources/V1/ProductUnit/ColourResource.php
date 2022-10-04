<?php

namespace App\Http\Resources\V1\ProductUnit;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ColourResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make('id', Schema::TYPE_INTEGER, 1),
            ResourceData::make('product_id', Schema::TYPE_INTEGER, 1),
            ...BaseColourResource::data(),
        ];
    }
}