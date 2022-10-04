<?php

namespace App\Http\Resources\V1\Lead;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class SubLeadCategoryResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make('id', Schema::TYPE_INTEGER, 1),
            ResourceData::make('name', Schema::TYPE_STRING, 'sub lead category name'),
            ResourceData::make('description', Schema::TYPE_STRING, 'sub lead category description'),
        ];
    }
}
