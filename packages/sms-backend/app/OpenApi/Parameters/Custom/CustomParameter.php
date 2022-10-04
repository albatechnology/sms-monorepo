<?php

namespace App\OpenApi\Parameters\Custom;

use App\OpenApi\Customs\Attributes\Parameters as ParametersAttribute;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use App\OpenApi\Parameters\GenericParameters;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;

class CustomParameter extends ParametersFactory
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
            GenericParameters::filterQueryParameter($this->model),
            GenericParameters::paginationQueryParameter(),
            GenericParameters::sortQueryParameter(),
            (new DefaultHeaderParameters())->build()
        );
    }
}









