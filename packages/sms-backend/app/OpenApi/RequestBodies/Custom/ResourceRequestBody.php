<?php

namespace App\OpenApi\RequestBodies\Custom;

use App\Classes\DocGenerator\BaseRequestBody;
use App\OpenApi\Customs\Attributes\RequestBody as RequestBodyAttribute;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;

class ResourceRequestBody extends BaseRequestBody
{
    public function customBuild(RequestBodyAttribute $attribute): RequestBody
    {
        $requestBody = $attribute->request;
        $this->requestClass = new $requestBody();
        return $this->build();
    }
}









