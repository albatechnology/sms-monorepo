<?php

namespace App\Http\Resources\V1\Qa;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use App\Http\Resources\V1\User\UserResource;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class BaseQaMessageResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("id", Schema::TYPE_INTEGER, 1),
            ResourceData::makeRelationship('sender', UserResource::class)->onNested(true),
            ResourceData::make('content', Schema::TYPE_STRING, 'Test Message'),
            ...ResourceData::timestamps()
        ];
    }
}