<?php

namespace App\Http\Resources\V1\Stock;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Illuminate\Support\Facades\DB;

class StockResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make('id', Schema::TYPE_INTEGER, 1),
            ResourceData::make('stock', Schema::TYPE_INTEGER, 1),
            ResourceData::make('indent', Schema::TYPE_INTEGER, 1),
            // ResourceData::make('total_stock', Schema::TYPE_INTEGER, 1)->value(function ($q) {
            //     return $q->select(DB::raw('sum(stock) as total_stock'))->groupBy('product_unit_id', 'channel_id')->first();
            // }),
            ResourceData::channel(),
            ResourceData::make('product_id', Schema::TYPE_INTEGER, 1),
            ...ResourceData::timestamps()
        ];
    }
}
