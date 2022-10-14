<?php

namespace App\Http\Resources\V1\Product;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class BaseProductBrandResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("id", Schema::TYPE_INTEGER, 1),
            ResourceData::make("name", Schema::TYPE_STRING, 'Product ABC')->sortable(),
        ];
    }
}
