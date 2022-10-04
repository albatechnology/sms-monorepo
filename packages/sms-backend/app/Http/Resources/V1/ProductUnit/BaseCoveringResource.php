<?php

namespace App\Http\Resources\V1\ProductUnit;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class BaseCoveringResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make('name', Schema::TYPE_STRING, 'Cover ABC'),
            ResourceData::make('type', Schema::TYPE_STRING, 'Soft cover')->nullable(),
            ResourceData::make('product_id', Schema::TYPE_INTEGER, 1),
        ];
    }
}