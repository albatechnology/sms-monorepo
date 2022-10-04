<?php

namespace App\OpenApi\Responses\Custom;

use App\Contracts\Errors;
use App\OpenApi\Customs\Attributes\ErrorResponse;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class CustomErrorResponse extends ResponseFactory
{
    public ?Response $response = null;

    public function build(): Response
    {
        return $this->response;
    }

    public function customBuild(ErrorResponse $response): Response
    {
        $error = Errors::getErrorByException($response->exception);

        return Response::create('ErrorResponse')
                       ->description($error['description'])
                       ->content(
                           MediaType::json()->schema(
                               Schema::object('data')
                                     ->properties(
                                         Schema::string('message')->example($error['label']),
                                         Schema::string('code')->example($error['error_code']),
                                     )
                                     ->required('message', 'code')
                           )
                       )->statusCode($error['http_code']);
    }
}
