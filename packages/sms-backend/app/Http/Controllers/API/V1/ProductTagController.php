<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Exceptions\UnauthorisedTenantAccessException;
use App\Http\Resources\V1\Product\ProductTagResource;
use App\Models\ProductTag;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class ProductTagController extends BaseApiController
{

    /**
     * Show all product tag.
     *
     * Show all product tag
     *
     */
    #[CustomOpenApi\Operation(id: 'productTagIndex', tags: [Tags::ProductTag, Tags::V1])]
    #[CustomOpenApi\Parameters(model: ProductTag::class)]
    #[CustomOpenApi\Response(resource: ProductTagResource::class, isCollection: true)]
    public function index()
    {
        return CustomQueryBuilder::buildResource(ProductTag::class, ProductTagResource::class, fn($query) => $query->tenanted());
    }


    /**
     * Get product tag
     *
     * Returns product tag by id
     *
     * @param ProductTag $product_tag
     * @return  ProductTagResource
     */
    #[CustomOpenApi\Operation(id: 'productTagShow', tags: [Tags::ProductTag, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: ProductTagResource::class, statusCode: 200)]
    #[CustomOpenApi\ErrorResponse(exception: UnauthorisedTenantAccessException::class)]
    public function show(ProductTag $product_tag)
    {
        return new ProductTagResource($product_tag->checkTenantAccess());
    }
}