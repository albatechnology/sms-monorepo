<?php

namespace App\Http\Resources\V1\Generic;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class MediaResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("id", Schema::TYPE_INTEGER, 1)->nullable(),
            ResourceData::make("name", Schema::TYPE_STRING, 'Test image'),
            ResourceData::make("mime_type", Schema::TYPE_STRING, 'image/png'),
            ResourceData::make("url", Schema::TYPE_STRING, ApiDataExample::IMAGE_URL),
            ResourceData::make("thumbnail", Schema::TYPE_STRING, ApiDataExample::IMAGE_URL),
            ResourceData::make("preview", Schema::TYPE_STRING, ApiDataExample::IMAGE_URL),
        ];
    }
}
