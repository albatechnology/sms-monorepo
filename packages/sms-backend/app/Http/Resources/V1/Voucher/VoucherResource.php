<?php

namespace App\Http\Resources\V1\Voucher;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class VoucherResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make('id', Schema::TYPE_STRING, 'GratisOngkir'),
            ResourceData::make('description', Schema::TYPE_STRING, 'Voucher description')->nullable(),
            ResourceData::make('value', Schema::TYPE_INTEGER, 1000000),
            ResourceData::make('min_order_price', Schema::TYPE_INTEGER, 1000000),
            ResourceData::make('start_time', Schema::TYPE_STRING, ApiDataExample::DATE),
            ResourceData::make('end_time', Schema::TYPE_STRING, ApiDataExample::DATE),
            ResourceData::make('is_active', Schema::TYPE_BOOLEAN, true),
        ];
    }
}
