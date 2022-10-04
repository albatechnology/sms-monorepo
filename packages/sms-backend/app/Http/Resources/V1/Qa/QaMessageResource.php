<?php

namespace App\Http\Resources\V1\Qa;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;

class QaMessageResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ...BaseQaMessageResource::data(),
            ResourceData::makeRelationship('topic', QaTopicResource::class),
        ];
    }
}