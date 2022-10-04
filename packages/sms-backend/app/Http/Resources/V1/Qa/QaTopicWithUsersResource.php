<?php

namespace App\Http\Resources\V1\Qa;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use App\Http\Resources\V1\User\UserResource;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class QaTopicWithUsersResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("id", Schema::TYPE_INTEGER, 1),
            ResourceData::make('subject', Schema::TYPE_STRING, 'New chat room'),
            ResourceData::makeRelationship('creator', UserResource::class),
            ResourceData::makeRelationshipCollection('users', UserResource::class),
            ...ResourceData::timestamps()
        ];
    }
}