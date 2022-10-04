<?php

namespace App\Http\Resources\V1\Activity;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use App\Http\Resources\V1\User\MinimumUserResource;
use App\Http\Resources\V1\User\UserResource;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Illuminate\Http\Resources\MissingValue;

class ActivityCommentResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("id", Schema::TYPE_INTEGER, 1),
            ResourceData::make('content', Schema::TYPE_STRING, 'my comment'),
            ResourceData::make('activity_id', Schema::TYPE_INTEGER, 1),
            ResourceData::make('activity_comment_id', Schema::TYPE_INTEGER, 1)
                        ->description('The activity comment id this comment replied to')
                        ->nullable(),
            ResourceData::make('activity_comment_content', Schema::TYPE_STRING, 'my comment')
                        ->value(function ($q) {
                            if ($q->resource->relationLoaded('activity_comment')) {
                                return $q->activity_comment ? $q->activity_comment->content : null;
                            }
                            return new MissingValue();
                        })
                        ->description('The activity comment content this comment replied to')
                        ->nullable(),

            ResourceData::makeRelationship('user', UserResource::class, on_nested: true),
            ...ResourceData::timestamps()
        ];
    }
}