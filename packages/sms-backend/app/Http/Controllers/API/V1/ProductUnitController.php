<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Exceptions\UnauthorisedTenantAccessException;
use App\Http\Requests\API\V1\ProductUnit\UploadProductUnitImageRequest;
use App\Http\Resources\V1\ProductUnit\ColourResource;
use App\Http\Resources\V1\ProductUnit\CoveringResource;
use App\Http\Resources\V1\ProductUnit\ProductUnitResource;
use App\Models\Colour;
use App\Models\Covering;
use App\Models\ProductUnit;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use App\OpenApi\RequestBodies\Custom\ImageRequestBody;
use App\OpenApi\Responses\Custom\GenericSuccessMessageResponse;
use Illuminate\Http\JsonResponse;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class ProductUnitController extends BaseApiController
{
    const load_relation = ['colour', 'covering'];

    /**
     * Get product unit
     *
     * Returns product unit by id
     *
     * @param ProductUnit $productUnit
     * @return  ProductUnitResource
     */
    #[CustomOpenApi\Operation(id: 'ProductUnitShow', tags: [Tags::ProductUnit, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: ProductUnitResource::class, statusCode: 200)]
    #[CustomOpenApi\ErrorResponse(exception: UnauthorisedTenantAccessException::class)]
    public function show(ProductUnit $productUnit)
    {
        return new ProductUnitResource($productUnit->loadMissing(self::load_relation)->checkTenantAccess());
    }

    /**
     * Show all product unit.
     *
     * Show all product unit
     *
     */
    #[CustomOpenApi\Operation(id: 'ProductUnitIndex', tags: [Tags::ProductUnit, Tags::V1])]
    #[CustomOpenApi\Parameters(model: ProductUnit::class)]
    #[CustomOpenApi\Response(resource: ProductUnitResource::class, isCollection: true)]
    public function index()
    {
        return CustomQueryBuilder::buildResource(
            ProductUnit::class,
            ProductUnitResource::class,
            fn($query) => $query->with(self::load_relation)->tenanted()->whereActive()
        );
    }

    /**
     * Show all product unit colours.
     *
     * Show all product unit colours
     *
     */
    #[CustomOpenApi\Operation(id: 'ProductUnitColours', tags: [Tags::ProductUnit, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Colour::class)]
    #[CustomOpenApi\Response(resource: ColourResource::class, isCollection: true)]
    public function colours()
    {
        return CustomQueryBuilder::buildResource(
            Colour::class,
            ColourResource::class,
            fn($query) => $query->tenanted()
        );
    }

    /**
     * Show all product unit coverings.
     *
     * Show all product unit coverings
     *
     */
    #[CustomOpenApi\Operation(id: 'ProductUnitCoverings', tags: [Tags::ProductUnit, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Covering::class)]
    #[CustomOpenApi\Response(resource: CoveringResource::class, isCollection: true)]
    public function coverings()
    {
        return CustomQueryBuilder::buildResource(
            Covering::class,
            CoveringResource::class,
            fn($query) => $query->tenanted(),
        );
    }

    /**
     * Upload product unit image
     *
     * Upload product unit image
     *
     * @param ProductUnit $productUnit
     * @param UploadProductUnitImageRequest $request
     * @return JsonResponse
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    #[CustomOpenApi\Operation(id: 'ProductUnitUpload', tags: [Tags::ProductUnit, Tags::V1])]
    #[CustomOpenApi\Parameters(model: ProductUnit::class)]
    #[OpenApi\RequestBody(factory: ImageRequestBody::class)]
    #[OpenApi\Response(factory: GenericSuccessMessageResponse::class)]
    public function uploadProductUnitImage(
        ProductUnit $productUnit,
        UploadProductUnitImageRequest $request,
    ): JsonResponse
    {
        $productUnit->addMedia($request->file('image'))->toMediaCollection('photo');

        return response()->json(['message' => 'success', 'data' => $productUnit]);
    }
}
