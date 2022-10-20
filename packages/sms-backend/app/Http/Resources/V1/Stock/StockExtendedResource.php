<?php

namespace App\Http\Resources\V1\Stock;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use App\Http\Resources\V1\Product\ProductBrandResource;
// use App\Http\Resources\V1\Product\ColourResource;
// use App\Http\Resources\V1\Product\CoveringResource;
use App\Http\Resources\V1\Product\ProductResource;
use App\Services\HelperService;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Illuminate\Http\Resources\MissingValue;

class StockExtendedResource extends BaseResource
{
    public static function data(): array
    {
        return array_merge(
            StockResource::data(),
            [
                ResourceData::makeRelationship(
                    'product_unit',
                    ProductResource::class,
                    data: fn ($q) => $q->relationLoaded('product') ? $q->product : new MissingValue()
                ),

                ResourceData::makeRelationship(
                    'product_brand',
                    ProductBrandResource::class,
                    data: fn ($q) => HelperService::getIfLoaded($q, 'product.product.brand'),
                ),


                // ResourceData::makeRelationship(
                //     'colour',
                //     ColourResource::class,
                //     data: fn ($q) => HelperService::getIfLoaded($q, 'product.colour'),

                // ),

                // ResourceData::makeRelationship(
                //     'covering',
                //     CoveringResource::class,
                //     data: fn ($q) => HelperService::getIfLoaded($q, 'product.covering'),
                // ),

                ResourceData::make('outstanding_order', Schema::TYPE_INTEGER, 1)->value(function ($query) {
                    return (int) \App\Services\StockService::outstandingOrder($query->company_id, $query->channel_id, $query->product_unit_id);
                }),

                ResourceData::make('outstanding_shipment', Schema::TYPE_INTEGER, 1)->value(function ($query) {
                    return (int) \App\Services\StockService::outstandingShipment($query->company_id, $query->channel_id, $query->product_unit_id);
                }),

                ResourceData::make('real_stock', Schema::TYPE_INTEGER, 1)->value(function ($query) {
                    $outstanding_shipment = (int) \App\Services\StockService::outstandingShipment($query->company_id, $query->channel_id, $query->product_unit_id);
                    return ($query->stock + $outstanding_shipment) - $query->indent;
                }),
            ]
        );
    }
}
