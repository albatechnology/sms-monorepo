<?php

namespace App\OpenApi\Responses\Generics;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class NotFoundResponse extends ResponseFactory
{

    public function build(): Response
    {
        return Response::create('GenericNotFoundMessage')
                       ->description('Not found response')
                       ->content(
                           MediaType::json('data')->schema(
                               Schema::object('data')
                                     ->properties(Schema::string("message")->example("Not Found!"))
                                     ->required('message')
                           )
                       )->statusCode(404);
    }
}
