<?php

namespace App\OpenApi\Customs\Attributes;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\BaseRequestBody;
use App\OpenApi\RequestBodies\Custom\ResourceRequestBody;
use Attribute;
use Vyuldashev\LaravelOpenApi\Attributes\RequestBody as RequestBodyAttribute;

#[Attribute(Attribute::TARGET_METHOD)]
class RequestBody extends RequestBodyAttribute
{
    public string $request;

    public function __construct(string $request)
    {
        if (!is_a($request, BaseApiRequest::class, true)) {
            throw new \Exception("CustomRequestBody attribute must be type BaseApiRequest class");
        }

        $this->request = $request;
        parent::__construct(ResourceRequestBody::class);
    }
}
