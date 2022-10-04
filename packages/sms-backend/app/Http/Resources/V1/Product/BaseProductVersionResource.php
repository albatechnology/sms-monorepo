<?php

namespace App\Http\Resources\V1\Product;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class BaseProductVersionResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("name", Schema::TYPE_STRING, 'Product ABC'),
            ResourceData::make("height", Schema::TYPE_STRING, "120")->nullable(),
            ResourceData::make("width", Schema::TYPE_STRING, "120")->nullable(),
            ResourceData::make("length", Schema::TYPE_STRING, "120")->nullable(),
            ResourceData::images(),
        ];
    }
}
