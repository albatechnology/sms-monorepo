<?php

namespace App\OpenApi\Parameters\User;

use App\OpenApi\Customs\Attributes\Parameters as ParametersAttribute;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;

class UserProductBrandsParameter extends ParametersFactory
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
                    ->name('filter[id]')
                    ->required(false)
                    ->example('1,2,3')
                    ->schema(Schema::string())
                    ->description('Set of ids, comma separated'),
                Parameter::query()
                    ->name('filter[name]')
                    ->required(false)
                    ->example('Lazboy')
                    ->schema(Schema::string())
                    ->description('Product brand name'),
                Parameter::query()
                    ->name('filter[company_id]')
                    ->required(false)
                    ->example(1)
                    ->schema(Schema::integer())
                    ->description('id of companies'),
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
