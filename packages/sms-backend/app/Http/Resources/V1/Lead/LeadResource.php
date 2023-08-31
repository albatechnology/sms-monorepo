<?php

namespace App\Http\Resources\V1\Lead;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use App\Enums\ActivityStatus;
use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Http\Resources\V1\Channel\ChannelResource;
use App\Http\Resources\V1\Customer\CustomerResource;
use App\Http\Resources\V1\Product\BaseProductBrandResource;
use App\Http\Resources\V1\User\UserResource;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class LeadResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("id", Schema::TYPE_INTEGER, 1),
            ResourceData::makeEnum("type", LeadType::class),
            ResourceData::makeEnum("status", LeadStatus::class),
            ResourceData::make('label', Schema::TYPE_STRING, 'my prospect')->description('User provided/auto generated description for the lead.'),
            ResourceData::make('interest', Schema::TYPE_STRING, 'Lagi Pengen LazyBoy'),
            ResourceData::makeRelationship('customer', CustomerResource::class),
            ResourceData::makeRelationship('user', UserResource::class),
            ResourceData::makeRelationship('lead_category', LeadCategoryResource::class, 'leadCategory'),
            ResourceData::makeRelationship('lead_sub_category', SubLeadCategoryResource::class, 'subLeadCategory'),
            ResourceData::makeRelationshipCollection('product_brands', BaseProductBrandResource::class, 'productBrands'),
            ResourceData::makeRelationship('channel', ChannelResource::class, 'channel'),
            ResourceData::make('is_unhandled', Schema::TYPE_BOOLEAN, true),
            ResourceData::make('has_activity', Schema::TYPE_BOOLEAN, true),
            ResourceData::makeEnum("last_activity_status", ActivityStatus::class),
            // ResourceData::channel(),
            ...ResourceData::timestamps()
        ];
    }


    public static function getSortableFields(): array
    {
        return ['id', 'status', 'updated_at'];
    }
}
