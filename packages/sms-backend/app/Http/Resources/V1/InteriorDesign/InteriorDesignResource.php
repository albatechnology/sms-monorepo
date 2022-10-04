<?php

namespace App\Http\Resources\V1\InteriorDesign;

use App\Classes\DocGenerator\BaseResource;

class InteriorDesignResource extends BaseResource
{
    public static function data(): array
    {
        $allows = ['id', 'name'];
        $resources = [];
        foreach (BaseInteriorDesignResource::data() as $d) {
            if (in_array($d->getKey(), $allows)) {
                $resources[] = $d;
            }
        }
        return $resources;
    }
}
