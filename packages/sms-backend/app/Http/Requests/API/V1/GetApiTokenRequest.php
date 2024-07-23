<?php

namespace App\Http\Requests\API\V1;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\RequestData;

class GetApiTokenRequest extends BaseApiRequest
{
    protected static function data()
    {
        return [
            RequestData::make("email", "string", 'admin@gmail.com', 'required|email'),
            RequestData::make("password", "string", "password", 'required'),
        ];
    }

    public function authorize()
    {
        return true;
    }
}
