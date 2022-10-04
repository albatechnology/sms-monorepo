<?php

namespace App\Http\Resources\V1\InteriorDesign;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class BaseInteriorDesignResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("id", Schema::TYPE_INTEGER, 1),
            ResourceData::make("bum_id", Schema::TYPE_INTEGER, 1),
            ResourceData::make("sales_id", Schema::TYPE_INTEGER, 1),
            ResourceData::make("religion_id", Schema::TYPE_INTEGER, 1),
            ResourceData::make("name", Schema::TYPE_STRING, 'Interior Design A'),
            ResourceData::make("company", Schema::TYPE_STRING, 'PT. Interior Design A'),
            ResourceData::make("owner", Schema::TYPE_STRING, 'Nikko Fe'),
            ResourceData::make("npwp", Schema::TYPE_STRING, '1122334455'),
            ResourceData::make("address", Schema::TYPE_STRING, 'Tangerang Selatan'),
            ResourceData::make("phone", Schema::TYPE_STRING, '085691977176'),
            ResourceData::make("email", Schema::TYPE_STRING, 'nikko@gmail.com'),
            ResourceData::make("bank_account_name", Schema::TYPE_STRING, 'BCA'),
            ResourceData::make("bank_account_holder", Schema::TYPE_STRING, 'Mr. Nikko Fe'),
        ];
    }
}
