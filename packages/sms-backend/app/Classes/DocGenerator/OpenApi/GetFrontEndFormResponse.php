<?php

namespace App\Classes\DocGenerator\OpenApi;


use App\Classes\DocGenerator\Enums\DataFormat;
use App\Classes\DocGenerator\Enums\FormType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class GetFrontEndFormResponse extends ResponseFactory
{
    public function build(): Response
    {
        $schema = Schema::object('FrontEndForm')
            ->properties(
                Schema::string('key')->example('title')->description('identifier of the form value'),
                Schema::string('label')->example('Customer Title')->description('Label for the form display'),
                Schema::string('formType')->enum(...FormType::getValues())->example(FormType::TEXT),
                Schema::string('dataFormat')->enum(...DataFormat::getValues())->example(DataFormat::DEFAULT),

                Schema::array('options')
                    ->items(
                        Schema::object()->properties(
                            Schema::string('value')->example('mr'),
                            Schema::string('label')->example('Mr.'),
                        )
                    ),
                Schema::string('validationRule')->example('required|string')->description('Laravel style validation rule'),
            );

        $response = Schema::object('data')->properties(
            Schema::array('data')->items($schema)
        );

        return Response::create('GetFrontEndFormSuccess')
            ->description('Successful response')
            ->content(
                MediaType::json('data')->schema($response)
            )->statusCode(200);
    }
}
