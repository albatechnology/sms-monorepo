<?php

namespace App\OpenApi\Responses\Custom;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class GenericSuccessMessageResponse extends ResponseFactory
{
    public static function getResponse(): \Illuminate\Http\JsonResponse
    {
        return response()->json(["data" => ["message" => "success"]]);
    }

    public function build(): Response
    {
        return Response::create('GenericSuccessMessage')
                       ->description('Successful response')
                       ->content(
                           MediaType::json('data')->schema(
                               Schema::object('data')
                                     ->properties(
                                         Schema::object('data')
                                               ->properties(Schema::string("message")->example("success"))
                                               ->required('message')
                                     )
                           )
                       )->statusCode(200);
    }
}
