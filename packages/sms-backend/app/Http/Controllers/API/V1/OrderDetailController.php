<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\DocGenerator\Enums\Tags;
use App\Exceptions\UnauthorisedTenantAccessException;
use App\Http\Resources\V1\OrderDetailResource;
use App\Models\OrderDetail;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use App\OpenApi\RequestBodies\Custom\ImageRequestBody;
use App\OpenApi\Responses\Custom\GenericSuccessMessageResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class OrderDetailController extends BaseApiController
{
    /**
     * Get orderDetail
     *
     * Returns orderDetail by id
     *
     * @param OrderDetail $orderDetail
     * @return  OrderDetailResource
     */
    #[CustomOpenApi\Operation(id: 'OrderDetailShow', tags: [Tags::OrderDetail, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: OrderDetailResource::class, statusCode: 200)]
    #[CustomOpenApi\ErrorResponse(exception: UnauthorisedTenantAccessException::class)]
    public function show(OrderDetail $orderDetail)
    {
        return new OrderDetailResource($orderDetail);
    }

    /**
     * Upload order detail product unit image
     *
     * Upload order detail product unit image
     *
     * @param OrderDetail $OrderDetail
     * @return JsonResponse
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    #[CustomOpenApi\Operation(id: 'OrderDetail', tags: [Tags::OrderDetail, Tags::V1])]
    #[CustomOpenApi\Parameters(model: OrderDetail::class)]
    #[OpenApi\RequestBody(factory: ImageRequestBody::class)]
    #[OpenApi\Response(factory: GenericSuccessMessageResponse::class)]
    public function uploadImage(OrderDetail $orderDetail, Request $request): JsonResponse
    {
        $request->validate(['image' => 'required|image|mimes:jpeg,png,jpg,svg|max:10240']);
        if (count($orderDetail->photo) > 0) {
            foreach ($orderDetail->photo as $media) {
                $media->delete();
            }
        }
        $orderDetail->addMedia($request->file('image'))->toMediaCollection('photo');
        $orderDetail->refresh();
        return response()->json(['message' => 'success', 'data' => $orderDetail]);
    }
}
