<?php

namespace App\OpenApi\Parameters;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;

class DefaultHeaderParameters extends ParametersFactory
{
    /**
     * @return Parameter[]
     */
    public function build(): array
    {
        return [
            Parameter::header()
                ->name('Accept')
                ->description('API call should request for json response')
                ->example('application/json')
                ->schema(Schema::string()),

            Parameter::header()
                ->name('Content-Type')
                ->description('API call should be json data')
                ->example('application/json')
                ->schema(Schema::string()),
        ];
    }
}
