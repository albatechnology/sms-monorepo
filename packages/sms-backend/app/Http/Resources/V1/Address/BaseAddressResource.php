<?php

namespace App\Http\Resources\V1\Address;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use App\Enums\AddressType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class BaseAddressResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make('address_line_1', Schema::TYPE_STRING, '1706 Wilkinson Locks Apt. 161'),
            ResourceData::make('address_line_2', Schema::TYPE_STRING, '7500 Thompson Mills Suite 103')->nullable(),
            ResourceData::make('address_line_3', Schema::TYPE_STRING, 'test line 3')->nullable(),
            ResourceData::make('postcode', Schema::TYPE_STRING, '12342')->nullable(),
            ResourceData::make('city', Schema::TYPE_STRING, 'Jakarta')->nullable(),
            ResourceData::make('country', Schema::TYPE_STRING, 'indonesia')->nullable(),
            ResourceData::make('province', Schema::TYPE_STRING, 'Jawa Barat')->nullable(),
            ResourceData::make('phone', Schema::TYPE_STRING, '(751) 204-1106')->nullable(),
            ResourceData::makeEnum('type', AddressType::class),
            ResourceData::make('customer_id', Schema::TYPE_INTEGER, 1)->value(fn($d) => (int)$d['customer_id']),
        ];
    }
}