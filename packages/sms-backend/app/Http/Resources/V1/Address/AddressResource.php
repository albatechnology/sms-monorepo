<?php

namespace App\Http\Resources\V1\Address;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class AddressResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("id", Schema::TYPE_INTEGER, 1),
            ...BaseAddressResource::data(),
            ...ResourceData::timestamps()
        ];
    }
}