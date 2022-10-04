<?php

namespace App\OpenApi\Customs\Builder;

use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use Vyuldashev\LaravelOpenApi\Attributes\RequestBody as RequestBodyAttribute;
use App\OpenApi\Customs\Attributes\RequestBody as CustomRequestBodyAttribute;
use Vyuldashev\LaravelOpenApi\Builders\Paths\Operation\RequestBodyBuilder;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\RequestBodyFactory;
use Vyuldashev\LaravelOpenApi\RouteInformation;

class CustomRequestBodyBuilder extends RequestBodyBuilder
{
    public function build(RouteInformation $route): ?RequestBody
    {
        /** @var RequestBodyAttribute|null $requestBody */
        $requestBody = $route->actionAttributes->first(static fn(object $attribute) => $attribute instanceof RequestBodyAttribute || $attribute instanceof CustomRequestBodyAttribute);

        if ($requestBody) {
            /** @var RequestBodyFactory $requestBodyFactory */
            $requestBodyFactory = app($requestBody->factory);

            if($requestBody instanceof CustomRequestBodyAttribute){
                $requestBody = $requestBodyFactory->customBuild($requestBody);
            }elseif($requestBody instanceof RequestBodyAttribute){
                $requestBody = $requestBodyFactory->build();
            }

            if ($requestBodyFactory instanceof Reusable) {
                return RequestBody::ref('#/components/requestBodies/' . $requestBody->objectId);
            }
        }

        return $requestBody;
    }
}
