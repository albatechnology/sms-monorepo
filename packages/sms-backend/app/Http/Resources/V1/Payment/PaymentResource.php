<?php

namespace App\Http\Resources\V1\Payment;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use App\Enums\PaymentStatus;
use App\Http\Resources\V1\Generic\MediaResource;
use App\Http\Resources\V1\User\UserResource;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class PaymentResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make('id', Schema::TYPE_INTEGER, 1),
            ResourceData::make('amount', Schema::TYPE_INTEGER, 1),
            ResourceData::make('reference', Schema::TYPE_STRING, 'payment-1')->nullable(),

            ResourceData::makeEnum('status', PaymentStatus::class),

            ResourceData::makeRelationship('payment_type', PaymentTypeResource::class),
            ResourceData::makeRelationship('added_by', UserResource::class),
//            ResourceData::makeRelationship(
//                'approved_by',
//                UserResource::class,
//                config: SchemaConfig::nullable()
//            ),

            ResourceData::make('order_id', Schema::TYPE_INTEGER, 1),
            // ResourceData::make('company_id', Schema::TYPE_INTEGER, 1),

            ResourceData::makeRelationshipCollection(
                'proof',
                MediaResource::class,
                'proof',
                fn($q) => $q->proof ?? []
            )->notNested(),

            ...ResourceData::timestamps()
        ];
    }
}
