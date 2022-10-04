<?php

namespace App\OpenApi\Parameters;

use App\OpenApi\Customs\Attributes\Parameters as ParametersAttribute;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;

class GetDiscountsParameter extends ParametersFactory
{
    public string $model;

    public function customBuild(ParametersAttribute $attribute): array
    {
        $this->model = $attribute->model;
        return $this->build();
    }

    public function build(): array
    {
        return array_merge(
            [
                Parameter::path()
                         ->name('code')
                         ->required(true)
                         ->example('ALDKSJ')
                         ->schema(Schema::string())
                         ->description('The discount activation code'),
            ],
            (new DefaultHeaderParameters())->build()
        );
    }
}









