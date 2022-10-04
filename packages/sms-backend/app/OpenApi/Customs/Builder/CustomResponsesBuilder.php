<?php

namespace App\OpenApi\Customs\Builder;

use App\OpenApi\Customs\Attributes\Response as CustomResponseAttribute;
use App\OpenApi\Customs\Attributes\ErrorResponse;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Vyuldashev\LaravelOpenApi\Attributes\Response as ResponseAttribute;
use Vyuldashev\LaravelOpenApi\Builders\Paths\Operation\ResponsesBuilder;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\RouteInformation;

class CustomResponsesBuilder extends ResponsesBuilder
{
    public function build(RouteInformation $route): array
    {
        return $route->actionAttributes
            ->filter(static fn(object $attribute) =>
                $attribute instanceof CustomResponseAttribute
                || $attribute instanceof ResponseAttribute
                || $attribute instanceof ErrorResponse
            )
            ->map(static function ($attribute) {
                $factory = app($attribute->factory);


                if ($attribute instanceof CustomResponseAttribute) {
                    $response = $factory->customBuild($attribute);
                }elseif ($attribute instanceof ErrorResponse) {
                    $response = $factory->customBuild($attribute);
                } elseif ($attribute instanceof ResponseAttribute) {
                    $response = $factory->build();
                }

                if ($factory instanceof Reusable) {
                    return Response::ref('#/components/responses/' . $response->objectId)
                        ->statusCode($attribute->statusCode)
                        ->description($attribute->description);
                }

                return $response;
            })
            ->values()
            ->toArray();
    }
}
