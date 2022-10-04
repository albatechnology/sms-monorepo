<?php

namespace App\Http\Resources\V1\User;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;

class TokenResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("token", "string", '1|qkt9nv6rBNEYY34h4yxYbjZ4Wo9YP7e0lCRFSdoO', null, fn($d) => $d["token"]),
        ];
    }
}
