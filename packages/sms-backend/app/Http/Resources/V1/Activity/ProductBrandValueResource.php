<?php

namespace App\Http\Resources\V1\Activity;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use App\Http\Resources\V1\Generic\MediaResource;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ProductBrandValueResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("id", Schema::TYPE_INTEGER, 1),
            ResourceData::make("estimated_value", Schema::TYPE_INTEGER, 1),
            ResourceData::make("order_value", Schema::TYPE_INTEGER, 1),
            ResourceData::make("name", Schema::TYPE_STRING, 'Natuzzi Goyang', value: function($data){
                return $data->productBrand->name;
            }),
            ResourceData::makeRelationshipCollection(
                'images',
                MediaResource::class,
                null,
                fn($q) => $q->productBrand->apiImages ?? []
            )->onNested(true),
        ];
    }
}
