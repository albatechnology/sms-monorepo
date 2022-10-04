<?php

namespace App\Http\Resources\V1\Channel;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ChannelCategoryResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("id", Schema::TYPE_INTEGER, 1),
            ResourceData::make("name", Schema::TYPE_STRING, 'PT Indonesia'),
            ResourceData::make("code", Schema::TYPE_STRING, 'C001'),
        ];
    }
}
