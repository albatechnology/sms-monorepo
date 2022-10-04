<?php

namespace App\Http\Resources\V1\Location;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class LocationResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("id", Schema::TYPE_INTEGER, 1),
            ResourceData::make("orlan_id", Schema::TYPE_STRING, '100WH'),
            ResourceData::make("name", Schema::TYPE_STRING, 'WAREHOUSE Normal WH'),
        ];
    }
}
