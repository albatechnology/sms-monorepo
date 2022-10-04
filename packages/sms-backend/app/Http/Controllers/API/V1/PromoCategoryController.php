<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Exceptions\UnauthorisedTenantAccessException;
use App\Http\Resources\V1\PromoCategory\PromoCategoryResource;
use App\Models\PromoCategory;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class PromoCategoryController extends BaseApiController
{
    const load_relation = [];

    /**
     * Get promo category
     *
     * Returns promo category by id
     *
     * @param PromoCategory $promoCategory
     * @return  PromoCategoryResource
     */
    #[CustomOpenApi\Operation(id: 'PromoCategoryShow', tags: [Tags::PromoCategory, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: PromoCategoryResource::class, statusCode: 200)]
    #[CustomOpenApi\ErrorResponse(exception: UnauthorisedTenantAccessException::class)]
    public function show(PromoCategory $promoCategory)
    {
        // $this->authorize('show', $promoCategory);
        return new PromoCategoryResource($promoCategory->checkTenantAccess());
    }

    /**
     * Show all promo categories.
     *
     * Show all promo categories.
     *
     */
    #[CustomOpenApi\Operation(id: 'PromoCategoryIndex', tags: [Tags::PromoCategory, Tags::V1])]
    #[CustomOpenApi\Parameters(model: PromoCategory::class)]
    #[CustomOpenApi\Response(resource: PromoCategoryResource::class, isCollection: true)]
    public function index()
    {
        return CustomQueryBuilder::buildResource(
            PromoCategory::class,
            PromoCategoryResource::class,
            function ($query) {
                return $query->tenanted()->whereHas('promos');
            },
        );
    }
}
