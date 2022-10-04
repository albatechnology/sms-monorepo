<?php

namespace App\Http\Resources\V1\User;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class SupervisorTypeResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make('id', Schema::TYPE_INTEGER, 1),
            ResourceData::make('name', Schema::TYPE_STRING, 'Store Manager'),
            ResourceData::make('level', Schema::TYPE_INTEGER, 1)->nullable(),
        ];
    }
}
