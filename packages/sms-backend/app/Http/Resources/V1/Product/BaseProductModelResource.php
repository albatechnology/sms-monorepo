<?php

namespace App\Http\Resources\V1\Product;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class BaseProductModelResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("name", Schema::TYPE_STRING, 'Product ABC'),
        ];
    }
}
