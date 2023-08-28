<?php

namespace App\Http\Resources\V1\CartDemand;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class CartDemandItemLineResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make('id', Schema::TYPE_INTEGER, 1),
            ResourceData::make('name', Schema::TYPE_STRING, 'test product'),
            ResourceData::make('price', Schema::TYPE_INTEGER, 50000),
            ResourceData::make('quantity', Schema::TYPE_INTEGER, 1),
            ResourceData::make('image', Schema::TYPE_STRING, 'https://trek.albatech.id/images/no-image.jpg'),
        ];
    }
}
