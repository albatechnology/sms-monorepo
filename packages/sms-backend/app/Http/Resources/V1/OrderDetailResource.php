<?php

namespace App\Http\Resources\V1;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use App\Enums\OrderDetailStatus;
use App\Http\Resources\V1\Generic\MediaResource;
use App\Http\Resources\V1\Product\BaseProductBrandResource;
use App\Http\Resources\V1\Product\BaseProductCategoryCodeResource;
use App\Http\Resources\V1\Product\BaseProductModelResource;
use App\Http\Resources\V1\Product\BaseProductResource;
use App\Http\Resources\V1\Product\BaseProductVersionResource;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class OrderDetailResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make('id', Schema::TYPE_INTEGER, 1)->nullable(),
            ResourceData::make('quantity', Schema::TYPE_INTEGER, 1),
            ResourceData::make('unit_price', Schema::TYPE_INTEGER, 1),
            ResourceData::make('total_discount', Schema::TYPE_INTEGER, 1),
            ResourceData::make('total_price', Schema::TYPE_INTEGER, 1),
            ResourceData::makeEnum('status', OrderDetailStatus::class),

            ResourceData::makeRelationship('product', BaseProductResource::class, data: fn($q) => $q->records['product'], on_nested: true),
            // ResourceData::makeRelationship('colour', BaseColourResource::class, data: fn($q) => $q->records['product_unit']['colour'], on_nested: true),
            // ResourceData::makeRelationship('covering', BaseCoveringResource::class, data: fn($q) => $q->records['product_unit']['covering'], on_nested: true),

            // ResourceData::makeRelationship('product', BaseProductResource::class, null, fn($q) => $q->records['product'], null, true),
            ResourceData::makeRelationship('brand', BaseProductBrandResource::class, null, fn($q) => $q->records['product']['brand'], null, true),
            // ResourceData::makeRelationship('model', BaseProductModelResource::class, null, fn($q) => $q->records['product']['model'], null, true),
            // ResourceData::makeRelationship('version', BaseProductVersionResource::class, null, fn($q) => $q->records['product']['version'], null, true),
            // ResourceData::makeRelationship('category_code', BaseProductCategoryCodeResource::class, null, fn($q) => $q->records['product']['category_code'], null, true),

            ResourceData::makeRelationshipCollection(
                'images',
                MediaResource::class,
                null,
                fn($q) => $q->records['images'] ?? []
            )->onNested(true),
            ResourceData::makeRelationshipCollection(
                'photo',
                MediaResource::class,
                null,
                fn($q) => $q->photo ?? []
            )->onNested(true)
        ];
    }
}
