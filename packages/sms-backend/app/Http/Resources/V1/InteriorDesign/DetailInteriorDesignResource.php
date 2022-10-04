<?php

namespace App\Http\Resources\V1\InteriorDesign;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;

class DetailInteriorDesignResource extends BaseResource
{
    public static function data(): array
    {
        return array_merge(BaseInteriorDesignResource::data(), [
            ResourceData::makeRelationship('religion', ReligionResource::class, 'religion'),
        ]);
    }
}
