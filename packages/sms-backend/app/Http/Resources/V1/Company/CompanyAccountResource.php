<?php

namespace App\Http\Resources\V1\Company;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class CompanyAccountResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make('id', Schema::TYPE_INTEGER, 1),
            ResourceData::make('name', Schema::TYPE_STRING, 'My Test Account #1'),
            ResourceData::make('bank_name', Schema::TYPE_STRING, 'BCA'),
            ResourceData::make('account_name', Schema::TYPE_STRING, 'Test Account'),
            ResourceData::make('account_number', Schema::TYPE_STRING, '123123123'),
        ];
    }
}
