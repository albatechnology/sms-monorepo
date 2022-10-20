<?php

namespace App\Http\Resources\V1\Product;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use App\Enums\ProductCategoryType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;


class ProductCategoryResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("id", Schema::TYPE_INTEGER, 1),
            ResourceData::make("name", Schema::TYPE_STRING, 'Category A'),
            // ResourceData::make("slug", Schema::TYPE_STRING, 'category-a'),
            ResourceData::make("description", Schema::TYPE_STRING, 'This is a product category, plain text.'),
            // ResourceData::make("parent_id", Schema::TYPE_INTEGER, 1)->nullable(),
            // ResourceData::make("level", Schema::TYPE_INTEGER, 0)->nullable(),
            // ResourceData::makeEnum('type', ProductCategoryType::class),
            ResourceData::image('photo'),
        ];
    }
}
