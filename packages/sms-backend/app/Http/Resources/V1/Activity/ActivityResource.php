<?php

namespace App\Http\Resources\V1\Activity;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use App\Classes\DocGenerator\ResourceData;
use App\Enums\ActivityFollowUpMethod;
use App\Enums\ActivityStatus;
use App\Http\Resources\V1\Customer\CustomerResource;
use App\Http\Resources\V1\Generic\MediaResource;
// use App\Http\Resources\V1\InteriorDesign\InteriorDesignResource;
use App\Http\Resources\V1\Lead\LeadResource;
use App\Http\Resources\V1\Order\OrderResource;
use App\Http\Resources\V1\Product\ProductBrandResource;
use App\Http\Resources\V1\User\UserResource;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ActivityResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make('id', Schema::TYPE_INTEGER, 1),
            ResourceData::make('follow_up_datetime', Schema::TYPE_STRING, ApiDataExample::TIMESTAMP),
            ResourceData::make('feedback', Schema::TYPE_STRING, 'Customer feedback'),
            ResourceData::makeEnum("follow_up_method", ActivityFollowUpMethod::class),
            ResourceData::makeEnum("status", ActivityStatus::class),
            ResourceData::channel(),
            ResourceData::makeRelationshipCollection('activity_brand_values', ProductBrandValueResource::class),
            ResourceData::makeRelationshipCollection('brands', ProductBrandResource::class),
            ResourceData::makeRelationship('order', OrderResource::class),
            ResourceData::makeRelationship('lead', LeadResource::class),
            ResourceData::makeRelationship('user', UserResource::class),
            ResourceData::makeRelationship('customer', CustomerResource::class),
            ResourceData::makeRelationship('latest_comment', ActivityCommentResource::class, 'latestComment'),
            // ResourceData::makeRelationship('interior_design', InteriorDesignResource::class, 'interiorDesign'),
            ResourceData::make('activity_comment_count', Schema::TYPE_INTEGER, 1),
            ResourceData::make('estimated_value', Schema::TYPE_INTEGER, 1)->nullable(),
            ResourceData::make('reminder_datetime', Schema::TYPE_STRING, ApiDataExample::TIMESTAMP)->nullable(),
            ResourceData::make('reminder_note', Schema::TYPE_STRING, 'Remind myself to follow up')->nullable(),
            ResourceData::makeRelationshipCollection(
                'images',
                MediaResource::class,
                null,
                fn ($q) => $q->image ?? []
            )->onNested(true),
            ...ResourceData::timestamps()
        ];
    }
}
