<?php

namespace App\Http\Resources\V1\Payment;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class PaymentTypeResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("id", Schema::TYPE_INTEGER, 1),
            ResourceData::make('name', Schema::TYPE_STRING, 'Bank BCA'),
            ResourceData::make('payment_category_id', Schema::TYPE_INTEGER, 1),
            ResourceData::images(),
        ];
    }
}