<?php

namespace App\OpenApi\Customs\Attributes;

use App\Classes\DocGenerator\BaseResource;
use App\OpenApi\Responses\Custom\SuccessResourceResponse;
use Attribute;
use Vyuldashev\LaravelOpenApi\Attributes\Response as ResponseAttribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Response extends ResponseAttribute
{
    public string $resource;
    public string $isCollection;

    public function __construct(string $resource, bool $isCollection = false, int $statusCode = 200, string $description = null)
    {
        if(!is_a($resource, BaseResource::class, true)){
            throw new \Exception("CustomResponse attribute resource must be type BaseResource class");
        }

        $this->resource = $resource;
        $this->isCollection = $isCollection;
        parent::__construct(SuccessResourceResponse::class, $statusCode, $description);
    }
}
