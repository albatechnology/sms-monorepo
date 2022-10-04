<?php

namespace App\Http\Resources\V1\ProductUnit;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class BaseColourResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make('name', Schema::TYPE_STRING, 'Red'),
            ResourceData::make('description', Schema::TYPE_STRING, '24')->nullable(),
        ];
    }
}