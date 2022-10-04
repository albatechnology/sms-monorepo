<?php

namespace App\Http\Resources\V1\Stock;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Illuminate\Support\Facades\DB;

class ExtendedDetailResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make('id', Schema::TYPE_INTEGER, 1),
            ResourceData::make('created_at', Schema::TYPE_STRING),
            ResourceData::make('invoice_number', Schema::TYPE_STRING),
            ResourceData::make('sales', Schema::TYPE_STRING)->value(function ($query) {
                return $query->user->name ?? '-';
            }),
            ResourceData::make('deal_at', Schema::TYPE_STRING),
            ResourceData::make('expected_shipping_datetime', Schema::TYPE_STRING),
            ...ResourceData::timestamps()
        ];
    }
}
