<?php

namespace App\Http\Resources\V1\Product;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;

class ProductResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ...BaseProductResource::data(),
            ResourceData::makeRelationship('product_category', ProductCategoryResource::class, 'productCategory'),
            ResourceData::makeRelationship('product_brand', BaseProductBrandResource::class, 'productBrand'),
            // ResourceData::makeRelationshipCollection('tags', TagResource::class),
            ResourceData::images(),

            // ResourceData::makeRelationship('brand', ProductBrandResource::class),
            // ResourceData::makeRelationship('model', ProductModelResource::class),
            // ResourceData::makeRelationship('version', ProductVersionResource::class),
            // ResourceData::makeRelationship('category_code', ProductCategoryCodeResource::class),
        ];
    }
}
