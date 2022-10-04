<?php

namespace App\OpenApi\Schemas;

use GoldSpecDigital\ObjectOrientedOAS\Contracts\SchemaContract;
use GoldSpecDigital\ObjectOrientedOAS\Objects\AllOf;
use GoldSpecDigital\ObjectOrientedOAS\Objects\AnyOf;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Not;
use GoldSpecDigital\ObjectOrientedOAS\Objects\OneOf;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;

class MediaSchema extends SchemaFactory implements Reusable
{
    /**
     * @return AllOf|OneOf|AnyOf|Not|Schema
     */
    public function build(): SchemaContract
    {
        return Schema::object('media')
            ->properties(
                Schema::integer('id')->example(1),
                Schema::string('name')->example('Test image'),
                Schema::string('fileName')->example('test.png'),
                Schema::string('mimeType')->example('binary/octet-stream'),
                Schema::integer('size')->example(100),
                Schema::string('url')->example('https://print-trail-media-dev.s3.eu-west-2.amazonaws.com/7533653a-a88c-45b8-89bd-b4e76241d6dc/test.png'),
            )
            ->required(
                Schema::string('url'),
            );
    }
}
