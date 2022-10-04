<?php

namespace App\Http\Resources\V1\Target;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use App\Enums\TargetChartType;
use App\Enums\TargetType;
use App\Http\Resources\V1\Report\ReportResource;
use App\Models\ProductBrand;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class TargetLineResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make('id', Schema::TYPE_INTEGER, 1),
            //ResourceData::make('model_type', Schema::TYPE_STRING, ProductBrand::class),
            //ResourceData::make('model_id', Schema::TYPE_INTEGER, 1),
            ResourceData::make('target_id', Schema::TYPE_INTEGER, 1),
            ResourceData::make('label', Schema::TYPE_STRING, ProductBrand::class),
            ResourceData::make('target', Schema::TYPE_INTEGER, 1),
            ResourceData::make('value', Schema::TYPE_INTEGER, 1),

            ...ResourceData::timestamps()
        ];
    }
}