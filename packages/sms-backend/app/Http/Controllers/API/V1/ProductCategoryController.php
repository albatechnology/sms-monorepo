<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Exceptions\UnauthorisedTenantAccessException;
use App\Http\Resources\V1\Product\ProductCategoryResource;
use App\Models\ProductCategory;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class ProductCategoryController extends BaseApiController
{

    /**
     * Get product category
     *
     * Returns product category by id
     *
     * @param ProductCategory $productCategory
     * @return  ProductCategoryResource
     */
    #[CustomOpenApi\Operation(id: 'ProductCategoryShow', tags: [Tags::ProductCategory, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: ProductCategoryResource::class, statusCode: 200)]
    #[CustomOpenApi\ErrorResponse(exception: UnauthorisedTenantAccessException::class)]
    public function show(ProductCategory $productCategory)
    {
        return new ProductCategoryResource($productCategory->checkTenantAccess());
    }

    /**
     * Show all product category.
     *
     * Show all product category
     *
     */
    #[CustomOpenApi\Operation(id: 'ProductCategoryIndex', tags: [Tags::ProductCategory, Tags::V1])]
    #[CustomOpenApi\Parameters(model: ProductCategory::class)]
    #[CustomOpenApi\Response(resource: ProductCategoryResource::class, isCollection: true)]
    public function index()
    {
        return CustomQueryBuilder::buildResource(ProductCategory::class, ProductCategoryResource::class, fn($query) => $query->tenanted());
        // return CustomQueryBuilder::buildResource(ProductCategory::class, ProductCategoryResource::class);
    }
}
