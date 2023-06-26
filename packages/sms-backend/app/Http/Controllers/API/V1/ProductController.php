<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Exceptions\UnauthorisedTenantAccessException;
use App\Http\Resources\V1\Product\ProductBrandResource;
use App\Http\Resources\V1\Product\ProductCategoryCodeResource;
use App\Http\Resources\V1\Product\ProductModelResource;
use App\Http\Resources\V1\Product\ProductResource;
use App\Http\Resources\V1\Product\ProductVersionResource;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategoryCode;
use App\Models\ProductModel;
use App\Models\ProductVersion;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use Illuminate\Http\Request;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class ProductController extends BaseApiController
{
    // const load_relation = ['brand', 'model', 'version', 'category_code'];
    const load_relation = ['brand', 'productCategory', 'productBrand'];

    /**
     * Get product
     *
     * Returns product by id
     *
     * @param Product $product
     * @return  ProductResource
     * @throws UnauthorisedTenantAccessException
     */
    #[CustomOpenApi\Operation(id: 'ProductShow', tags: [Tags::Product, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: ProductResource::class, statusCode: 200)]
    #[CustomOpenApi\ErrorResponse(exception: UnauthorisedTenantAccessException::class)]
    public function show(Product $product): ProductResource
    {
        return new ProductResource($product->loadMissing(self::load_relation)->checkTenantAccess());
    }

    /**
     * Show all product.
     *
     * Show all product
     *
     */
    #[CustomOpenApi\Operation(id: 'ProductIndex', tags: [Tags::Product, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Product::class)]
    #[CustomOpenApi\Response(resource: ProductResource::class, isCollection: true)]
    public function index()
    {
        $query = function ($query) {
            return $query->with(self::load_relation)->tenanted()->whereActive();
            // return $query->with(self::load_relation)->whereActive();
        };

        return CustomQueryBuilder::buildResource(Product::class, ProductResource::class, $query);
    }

    /**
     * Show all brands.
     *
     * Show all product brands available for the active company
     *
     */
    #[CustomOpenApi\Operation(id: 'ProductBrand', tags: [Tags::Product, Tags::V1])]
    #[CustomOpenApi\Parameters(model: CustomQueryBuilder::KEY_ID_NAME)]
    #[CustomOpenApi\Response(resource: ProductBrandResource::class, isCollection: true)]
    public function brands()
    {
        return CustomQueryBuilder::buildResource(
            ProductBrand::class,
            ProductBrandResource::class,
            fn ($q) => $q->tenanted(),
            filter_key: CustomQueryBuilder::KEY_ID_NAME
        );
    }

    /**
     * Show all product model.
     *
     * Show all product model available.
     * @param Request $request
     * @return mixed
     */
    #[CustomOpenApi\Operation(id: 'ProductModel', tags: [Tags::Product, Tags::V1])]
    #[CustomOpenApi\Parameters(model: ProductModel::class)]
    #[CustomOpenApi\Response(resource: ProductModelResource::class, isCollection: true)]
    public function models(Request $request): mixed
    {
        $query = fn ($q) => $q->tenanted()->with(['media']);
        return CustomQueryBuilder::buildResource(ProductModel::class, ProductModelResource::class, $query);
    }

    /**
     * Get product model by id.
     *
     * Get product model for a given id.
     * @param ProductModel $model
     * @return ProductModelResource
     * @throws UnauthorisedTenantAccessException
     */
    #[CustomOpenApi\Operation(id: 'ProductModelById', tags: [Tags::Product, Tags::V1])]
    #[CustomOpenApi\Parameters(model: ProductModel::class)]
    #[CustomOpenApi\Response(resource: ProductModelResource::class)]
    public function model(ProductModel $model): ProductModelResource
    {
        return new ProductModelResource($model->checkTenantAccess());
    }

    /**
     * Show all product version.
     *
     * Show all product version available.
     * @param Request $request
     * @return mixed
     */
    #[CustomOpenApi\Operation(id: 'ProductVersion', tags: [Tags::Product, Tags::V1])]
    #[CustomOpenApi\Parameters(model: ProductVersion::class)]
    #[CustomOpenApi\Response(resource: ProductVersionResource::class, isCollection: true)]
    public function versions(Request $request): mixed
    {
        $products = Product::tenanted();
        if ($request->query('product_brand_id')) $products = $products->where('product_brand_id', $request->query('product_brand_id'));
        if ($request->query('product_model_id')) $products = $products->where('product_model_id', $request->query('product_model_id'));

        $ids   = $products->get('product_version_id')->pluck('product_version_id')->unique()->values();
        $query = fn ($q) => $q->whereIn('id', $ids);

        return CustomQueryBuilder::buildResource(ProductVersion::class, ProductVersionResource::class, $query);
    }

    /**
     * Show all product version.
     *
     * Show all product version available.
     * @param Request $request
     * @return mixed
     */
    #[CustomOpenApi\Operation(id: 'ProductCategoryCodes', tags: [Tags::Product, Tags::V1])]
    #[CustomOpenApi\Parameters(model: ProductCategoryCode::class)]
    #[CustomOpenApi\Response(resource: ProductCategoryCodeResource::class, isCollection: true)]
    public function categoryCodes(Request $request): mixed
    {
        $products = Product::tenanted();
        if ($request->query('product_brand_id')) $products = $products->where('product_brand_id', $request->query('product_brand_id'));
        if ($request->query('product_model_id')) $products = $products->where('product_model_id', $request->query('product_model_id'));
        if ($request->query('product_version_id')) $products = $products->where('product_version_id', $request->query('product_version_id'));

        $ids   = $products->get('product_category_code_id')->pluck('product_category_code_id')->unique()->values();
        $query = fn ($q) => $q->whereIn('id', $ids);

        return CustomQueryBuilder::buildResource(ProductCategoryCode::class, ProductCategoryCodeResource::class, $query);
    }
}
