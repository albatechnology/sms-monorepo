<?php

namespace App\Http\Resources\V1\Lead;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use App\Classes\DocGenerator\SchemaConfig;
use App\Http\Resources\V1\Activity\ActivityResource;

class LeadWithLatestActivityResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ...LeadResource::data(),
            ResourceData::makeRelationship(
                'latest_activity', ActivityResource::class,
                null, fn($d) => $d->latestActivity ?? null,
                new SchemaConfig(true, 'The latest activity sorted by creation time.')
            ),
        ];
    }
}