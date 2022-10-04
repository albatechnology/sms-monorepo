<?php

namespace App\Http\Resources\V1\Product;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class BaseProductResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("name", Schema::TYPE_STRING, 'Product ABC'),
            ResourceData::make("price", Schema::TYPE_INTEGER, 100000),
            ResourceData::make("video_url", Schema::TYPE_STRING, 'Product video URL'),
            ResourceData::make("description", Schema::TYPE_STRING, 'Product description'),
        ];
    }
}
