<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Http\Resources\V1\Discount\DiscountResource;
use App\Models\Discount;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
// use App\OpenApi\Parameters\DefaultHeaderParameters;
use App\OpenApi\Parameters\GetDiscountsParameter;
use App\OpenApi\Responses\Generics\NotFoundResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Request;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;


#[OpenApi\PathItem]
class DiscountController extends BaseApiController
{
    const load_relation = [];

    /**
     * Get discounts
     *
     * Get all public discounts.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    #[CustomOpenApi\Operation(id: 'DiscountIndex', tags: [Tags::Cart, Tags::Discount, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Discount::class)]
    #[CustomOpenApi\Response(resource: DiscountResource::class, isCollection: true)]
    public function index()
    {
        // return CustomQueryBuilder::buildResource(Discount::class, DiscountResource::class, fn ($q) => $q->tenanted()->whereActive());
        return CustomQueryBuilder::buildResource(Discount::class, DiscountResource::class, fn ($q) => $q->tenanted()->whereActive());
    }

    /**
     * Get discount by code
     *
     * Get discount that match the given discount code.
     *
     * @param $code
     * @return DiscountResource
     */
    #[CustomOpenApi\Operation(id: 'DiscountGetByCode', tags: [Tags::Cart, Tags::Discount, Tags::V1])]
    #[OpenApi\Parameters(factory: GetDiscountsParameter::class)]
    #[CustomOpenApi\Response(resource: DiscountResource::class)]
    #[OpenApi\Response(factory: NotFoundResponse::class, statusCode: 404)]
    public function discountGetByCode($code)
    {
        // $discounts = Discount::tenanted()
        $discounts = Discount::tenanted()->whereActive($code)
            ->firstOrFail();

        return new DiscountResource($discounts);
    }
}
