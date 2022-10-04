<?php

namespace App\OpenApi\Schemas\Metadata;

use App\Classes\DocGenerator\Enums\DataFormat;
use GoldSpecDigital\ObjectOrientedOAS\Contracts\SchemaContract;
use GoldSpecDigital\ObjectOrientedOAS\Objects\AllOf;
use GoldSpecDigital\ObjectOrientedOAS\Objects\AnyOf;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Not;
use GoldSpecDigital\ObjectOrientedOAS\Objects\OneOf;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;

class QuerySchema extends SchemaFactory implements Reusable
{
    /**
     * @return AllOf|OneOf|AnyOf|Not|Schema
     */
    public function build(): SchemaContract
    {
        return Schema::object('query')->properties(
            Schema::array('filters')->items(
                Schema::object()->properties(
                    Schema::string('key')->example("name"),
                    Schema::string('dataFormat')->enum(...DataFormat::getValues())->example(DataFormat::DEFAULT),
                    Schema::array('options')
                          ->items(
                              Schema::object()->properties(
                                  Schema::string('value')->example('COLLECTION'),
                                  Schema::string('label')->example('Collection'),
                              )
                          )
                          ->nullable()
                          ->description('Option will be provided when filter is based on enum options')
                ),
            ),
            Schema::array('sort')->items(
                Schema::string('key'),
            )
        );
    }
}
