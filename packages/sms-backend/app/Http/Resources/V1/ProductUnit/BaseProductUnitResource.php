<?php

namespace App\Http\Resources\V1\ProductUnit;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class BaseProductUnitResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make('id', Schema::TYPE_INTEGER, 1, cast: 'int'),
            ResourceData::make('name', Schema::TYPE_STRING, 'product unit name'),
            ResourceData::make('price', Schema::TYPE_INTEGER, 100000, cast: 'int'),
            ResourceData::make('volume', Schema::TYPE_INTEGER, 10, cast: 'float'),
            ResourceData::make('production_cost', Schema::TYPE_INTEGER, 100000, value: function($data){
                $production_cost = 0;
                if($data['production_cost'] > 10) {
                    $production_cost = $data['production_cost'] ?? 0;
                } elseif ($data['production_cost'] < 10 && isset($data['calculated_hpp']) && $data['calculated_hpp'] != null) {
                    $production_cost = $data['calculated_hpp'] ?? 0;
                }

                return $production_cost ?? 0;
            }),
            ResourceData::makeRelationship('colour', ColourResource::class),
            ResourceData::makeRelationship('covering', CoveringResource::class),
        ];
    }
}
