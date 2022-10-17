<?php

namespace App\OpenApi\Parameters\Lead;

use App\OpenApi\Parameters\DefaultHeaderParameters;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;

class LeadProductBrandsParameter extends ParametersFactory
{
    public function build(): array
    {
        return array_merge(
            [
                Parameter::query()
                    ->name('available_product_brands')
                    ->required(false)
                    ->example('1,2,3')
                    ->schema(
                        Schema::boolean()
                            ->default(false)
                    )
                    ->description('menampilkan product brands lead yang belum dipilih'),
            ],
            (new DefaultHeaderParameters())->build()
        );
    }
}
