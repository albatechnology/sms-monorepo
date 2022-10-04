<?php

namespace App\Http\Resources\V1\Product;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ProductResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("id", Schema::TYPE_INTEGER, 1),
            ...BaseProductResource::data(),
            ResourceData::makeRelationshipCollection('categories', ProductCategoryResource::class),
            ResourceData::makeRelationshipCollection('tags', TagResource::class),
            ResourceData::images(),

            ResourceData::makeRelationship('brand', ProductBrandResource::class),
            ResourceData::makeRelationship('model', ProductModelResource::class),
            ResourceData::makeRelationship('version', ProductVersionResource::class),
            ResourceData::makeRelationship('category_code', ProductCategoryCodeResource::class),
        ];
    }
}
