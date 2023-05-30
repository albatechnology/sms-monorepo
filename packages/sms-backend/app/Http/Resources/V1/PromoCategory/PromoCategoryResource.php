<?php

namespace App\Http\Resources\V1\PromoCategory;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use App\Http\Resources\V1\Company\BaseCompanyResource;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class PromoCategoryResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make('id', Schema::TYPE_INTEGER, 1),
            ResourceData::make('name', Schema::TYPE_STRING, 'promo category test'),
            ResourceData::make('description', Schema::TYPE_STRING, 'promo category test')->nullable(),
            ResourceData::images(),
            // ResourceData::makeRelationship('company', BaseCompanyResource::class),
        ];
    }
}
