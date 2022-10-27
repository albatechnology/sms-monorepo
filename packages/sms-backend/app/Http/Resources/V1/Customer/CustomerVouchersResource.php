<?php

namespace App\Http\Resources\V1\Customer;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use App\Http\Resources\V1\Voucher\VoucherResource;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class CustomerVouchersResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make('voucher_id', Schema::TYPE_STRING, 'GratisOngkir'),
            ResourceData::make('customer_id', Schema::TYPE_INTEGER, 1),
            ResourceData::make('is_used', Schema::TYPE_BOOLEAN, true),
            ResourceData::makeRelationship('voucher', VoucherResource::class),
        ];
    }

    public static function getSortableFields(): array
    {
        return ['voucher_id', 'is_used'];
    }
}
