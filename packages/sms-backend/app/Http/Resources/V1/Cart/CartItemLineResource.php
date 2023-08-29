<?php

namespace App\Http\Resources\V1\Cart;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
// use App\Http\Resources\V1\Product\ColourResource;
// use App\Http\Resources\V1\Product\CoveringResource;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class CartItemLineResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make('id', Schema::TYPE_INTEGER, 1),
            // ResourceData::make('sku', Schema::TYPE_STRING, '1441068311742'),
            ResourceData::make('quantity', Schema::TYPE_INTEGER, 10),
            ResourceData::make('name', Schema::TYPE_STRING, 'test product'),
            ResourceData::make('unit_price', Schema::TYPE_INTEGER, 100)->description('Price per item quantity'),
            ResourceData::make('total_discount', Schema::TYPE_INTEGER, 500)->description('Total discount for this item line'),
            ResourceData::make('total_price', Schema::TYPE_INTEGER, 500)->description('Total price for this item line including discount'),
            // ResourceData::makeRelationship('colour', ColourResource::class, null, fn($d) => $d['colour'])->onNested(false),
            // ResourceData::makeRelationship('covering', CoveringResource::class, null, fn($d) => $d['covering'])->onNested(false),
        ];
    }
}
