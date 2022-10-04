<?php

namespace App\OpenApi\Schemas\Metadata;

use GoldSpecDigital\ObjectOrientedOAS\Contracts\SchemaContract;
use GoldSpecDigital\ObjectOrientedOAS\Objects\AllOf;
use GoldSpecDigital\ObjectOrientedOAS\Objects\AnyOf;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Not;
use GoldSpecDigital\ObjectOrientedOAS\Objects\OneOf;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;

class MetaSchema extends SchemaFactory implements Reusable
{
    /**
     * @return AllOf|OneOf|AnyOf|Not|Schema
     */
    public function build(): SchemaContract
    {
        return Schema::object('meta')
            ->properties(
                Schema::integer('current_page')->nullable(),
                Schema::integer('from')->nullable(),
                //Schema::integer('last_page')->nullable(),
                Schema::string('path')->nullable(),
                Schema::integer('per_page')->nullable(),
                Schema::integer('to')->nullable(),
                // Schema::integer('total')->nullable(),
            );
    }
}
