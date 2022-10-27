<?php

namespace App\OpenApi\Parameters\Customer;

use App\OpenApi\Parameters\DefaultHeaderParameters;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;

class CustomerVouchersParameter extends ParametersFactory
{
    // public string $model;

    // public function customBuild(ParametersAttribute $attribute): array
    // {
    //     $this->model = $attribute->model;
    //     return $this->build();
    // }

    public function build(): array
    {
        return array_merge(
            [
                Parameter::query()
                    ->name('filter[customer_id]')
                    ->required(false)
                    ->example(1)
                    ->schema(Schema::integer())
                    ->description('id of customer'),
                Parameter::query()
                    ->name('filter[lead_id]')
                    ->required(false)
                    ->example(1)
                    ->schema(Schema::integer())
                    ->description('id of leads'),
            ],
            (new DefaultHeaderParameters())->build()
        );
    }
}
