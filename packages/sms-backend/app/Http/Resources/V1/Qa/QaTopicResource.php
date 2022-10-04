<?php

namespace App\Http\Resources\V1\Qa;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use App\Classes\DocGenerator\SchemaConfig;
use App\Http\Resources\V1\User\UserResource;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class QaTopicResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("id", Schema::TYPE_INTEGER, 1),
            ResourceData::make('subject', Schema::TYPE_STRING, 'New chat room'),
            ResourceData::makeRelationship('creator', UserResource::class),

            ResourceData::makeRelationship(
                'latest_message', BaseQaMessageResource::class,
                'latestMessage', null,
                new SchemaConfig('true', 'The latest message sent to this topic.')
            ),

            ResourceData::images(),
            ...ResourceData::timestamps()
        ];
    }
}