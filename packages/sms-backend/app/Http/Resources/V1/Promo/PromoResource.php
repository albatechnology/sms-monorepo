<?php

namespace App\Http\Resources\V1\Promo;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use App\Classes\DocGenerator\ResourceData;
use App\Http\Resources\V1\Company\BaseCompanyResource;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class PromoResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make('id', Schema::TYPE_INTEGER, 1),
            ResourceData::make('name', Schema::TYPE_STRING, 'promo test'),
            ResourceData::make('description', Schema::TYPE_STRING, 'promo test')->nullable(),
            ResourceData::make('start_time', Schema::TYPE_STRING, ApiDataExample::TIMESTAMP),
            ResourceData::make('end_time', Schema::TYPE_STRING, ApiDataExample::TIMESTAMP)->nullable(),
            ResourceData::images(),

            ResourceData::make('promo_category_id', Schema::TYPE_INTEGER, 1)->nullable(),
            ResourceData::make('lead_category_id', Schema::TYPE_INTEGER, 1)->nullable(),
            // ResourceData::makeRelationship('company', BaseCompanyResource::class),
        ];
    }
}
