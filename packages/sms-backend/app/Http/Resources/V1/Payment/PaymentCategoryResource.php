<?php

namespace App\Http\Resources\V1\Payment;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class PaymentCategoryResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("id", Schema::TYPE_INTEGER, 1),
            ResourceData::make('name', Schema::TYPE_STRING, 'Bank transfer'),
            ResourceData::images(),
        ];
    }
}