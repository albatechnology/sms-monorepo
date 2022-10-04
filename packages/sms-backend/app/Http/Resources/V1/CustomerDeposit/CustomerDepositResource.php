<?php

namespace App\Http\Resources\V1\CustomerDeposit;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use App\Classes\DocGenerator\ResourceData;
use App\Http\Resources\V1\Customer\CustomerResource;
use App\Http\Resources\V1\Lead\LeadResource;
use App\Http\Resources\V1\Order\OrderResource;
use App\Http\Resources\V1\Product\ProductBrandResource;
use App\Http\Resources\V1\User\UserResource;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class CustomerDepositResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make('id', Schema::TYPE_INTEGER, 1),
            ResourceData::make('follow_up_datetime', Schema::TYPE_STRING, ApiDataExample::TIMESTAMP),
            ResourceData::make('feedback', Schema::TYPE_STRING, 'Customer feedback'),
            ResourceData::channel(),
            ResourceData::makeRelationshipCollection('brands', ProductBrandResource::class),
            ResourceData::makeRelationship('order', OrderResource::class),
            ResourceData::makeRelationship('lead', LeadResource::class),
            ResourceData::makeRelationship('user', UserResource::class),
            ResourceData::makeRelationship('customer', CustomerResource::class),
            ResourceData::make('estimated_value', Schema::TYPE_INTEGER, 1)->nullable(),
            ResourceData::make('reminder_datetime', Schema::TYPE_STRING, ApiDataExample::TIMESTAMP)->nullable(),
            ResourceData::make('reminder_note', Schema::TYPE_STRING, 'Remind myself to follow up')->nullable(),
            ...ResourceData::timestamps()
        ];
    }
}
