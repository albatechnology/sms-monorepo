<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Exceptions\UnauthorisedTenantAccessException;
use App\Http\Resources\V1\Promo\PromoResource;
use App\Models\Promo;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class PromoController extends BaseApiController
{
    const load_relation = [];

    /**
     * Get promo
     *
     * Returns promo by id
     *
     * @param Promo $promo
     * @return  PromoResource
     */
    #[CustomOpenApi\Operation(id: 'PromoShow', tags: [Tags::Promo, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: PromoResource::class, statusCode: 200)]
    #[CustomOpenApi\ErrorResponse(exception: UnauthorisedTenantAccessException::class)]
    public function show(Promo $promo)
    {
        $this->authorize('show', $promo);
        return new PromoResource($promo->loadMissing(self::load_relation)->checkTenantAccess());
    }

    /**
     * Show all promo.
     *
     * Show all promo
     *
     */
    #[CustomOpenApi\Operation(id: 'PromoIndex', tags: [Tags::Promo, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Promo::class)]
    #[CustomOpenApi\Response(resource: PromoResource::class, isCollection: true)]
    public function index()
    {
        return CustomQueryBuilder::buildResource(
            Promo::class,
            PromoResource::class,
            function ($query) {

                $query = $query->with(self::load_relation)->tenanted();
                // $query = $query->with(self::load_relation);

                $filter = request()->get('filter');

                // if no time filter set, show all target that includes now
                if (!isset($filter['start_after']) && !isset($filter['end_before'])) {
                    $query = $query->targetDatetime(now());
                }

                return $query;
            },
        );
    }
}
