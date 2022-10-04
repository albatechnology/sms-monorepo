<?php

namespace App\OpenApi\Customs\Builder;

use App\OpenApi\Customs\Attributes\Parameters as CustomParametersAttribute;
use Illuminate\Support\Collection;
use Vyuldashev\LaravelOpenApi\Attributes\Parameters as ParametersAttribute;
use Vyuldashev\LaravelOpenApi\Builders\Paths\Operation\ParametersBuilder;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;
use Vyuldashev\LaravelOpenApi\RouteInformation;

class CustomParametersBuilder extends ParametersBuilder
{
    public function build(RouteInformation $route): array
    {
        $pathParameters = $this->buildPath($route);
        $attributedParameters = $this->buildAttribute($route);

        return $pathParameters->merge($attributedParameters)->toArray();
    }

    protected function buildAttribute(RouteInformation $route): Collection
    {
        /** @var ParametersAttribute|null $parameters */
        $parameters = $route->actionAttributes->first(static fn($attribute) => $attribute instanceof ParametersAttribute, []);

        if ($parameters) {
            /** @var ParametersFactory $parametersFactory */
            $parametersFactory = app($parameters->factory);

            if ($parameters instanceof CustomParametersAttribute) {
                $parameters = $parametersFactory->customBuild($parameters);
            } elseif ($parameters instanceof ParametersAttribute) {
                $parameters = $parametersFactory->build();
            }
        }

        return collect($parameters);
    }
}
