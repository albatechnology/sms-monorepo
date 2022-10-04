<?php

namespace App\OpenApi\Customs\Attributes;

use App\Exceptions\CustomApiException;
use App\OpenApi\Responses\Custom\CustomErrorResponse;
use Attribute;
use Vyuldashev\LaravelOpenApi\Attributes\Response;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class ErrorResponse extends Response
{
    public string $exception;
    public string $isCollection;

    public function __construct(string $exception)
    {
        if (!is_a($exception, CustomApiException::class, true)) {
            throw new \Exception("ErrorResponse attribute resource must extend from CustomApiException class");
        }

        $this->exception = $exception;
        parent::__construct(CustomErrorResponse::class);
    }
}
