<?php

namespace App\Http\Resources\V1\Report;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use App\Classes\DocGenerator\ResourceData;
use App\Enums\ReportableType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ReportResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make('id', Schema::TYPE_INTEGER, 1),
            ResourceData::make('name', Schema::TYPE_STRING, 'test report'),
            ResourceData::make('start_date', Schema::TYPE_STRING, ApiDataExample::TIMESTAMP)->nullable(),
            ResourceData::make('end_date', Schema::TYPE_STRING, ApiDataExample::TIMESTAMP)->nullable(),

            ResourceData::make('reportable_label', Schema::TYPE_STRING, 'test report'),
            ResourceData::makeEnum('reportable_type', ReportableType::class),
            ResourceData::make('reportable_id', Schema::TYPE_INTEGER, 1),
            // TODO: add user object here is type is user

            ...ResourceData::timestamps()
        ];
    }
}