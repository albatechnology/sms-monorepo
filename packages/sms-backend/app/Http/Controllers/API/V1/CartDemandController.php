<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\DocGenerator\Enums\Tags;
use App\Http\Requests\API\V1\CartDemand\SyncCartDemandRequest;
use App\Http\Resources\V1\CartDemand\CartDemandResource;
use App\Models\Cart;
use App\Models\CartDemand;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use App\OpenApi\RequestBodies\Custom\ImageRequestBody;
use App\OpenApi\Responses\Custom\GenericSuccessMessageResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;


#[OpenApi\PathItem]
class CartDemandController extends BaseApiController
{
    const load_relation = [];

    /**
     * Show user cart demand.
     *
     * Show cart demand of logged in user
     */
    #[CustomOpenApi\Operation(id: 'CartDemandIndex', tags: [Tags::CartDemand, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: CartDemandResource::class, statusCode: 200)]
    public function index()
    {
        return new CartDemandResource(tenancy()->getUser()->cartDemand()->whereNotOrdered()->first() ?? new CartDemand());
    }

    /**
     * Sync cart demand
     *
     * Sync user cart demand content
     *
     * @param SyncCartDemandRequest $request
     * @return CartDemandResource
     */
    #[CustomOpenApi\Operation(id: 'CartDemandSync', tags: [Tags::CartDemand, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: SyncCartDemandRequest::class)]
    #[CustomOpenApi\Response(resource: CartDemandResource::class, statusCode: 200)]
    public function sync(SyncCartDemandRequest $request): CartDemandResource
    {
        $user = tenancy()->getUser();
        $cartDemand = CartDemand::where('user_id', $user->id)->whereNotOrdered()->first();
        $requestItems = $request['items'];
        if (!$cartDemand) {
            $cartDemand = new CartDemand;
            $cartDemand->user_id = $user->id;
            $items = $requestItems;
        } else {
            $cartDemandItems = $cartDemand->items;
            $items = array_merge($cartDemandItems, $requestItems);
        }

        $total_price = collect($items)->sum(function ($i) {
            return $i['price'] * $i['quantity'];
        });
        $items = collect($items)->map(function ($item, $id) {
            $item['id'] = $id + 1;
            return $item;
        })->toArray();

        $cartDemand->total_price = $total_price;
        $cartDemand->items = $items;
        $cartDemand->save();
        return new CartDemandResource($cartDemand);
    }

    /**
     * Delete cart demand
     *
     * Delete a cart demand by its id
     *
     * @param cart $cart
     * @return JsonResponse
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'CartDemandDestroy', tags: [Tags::CartDemand, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GenericSuccessMessageResponse::class)]
    public function destroy($id)
    {
        $cartDemand = tenancy()->getUser()->cartDemand()->whereNotOrdered()->first();
        $items = $cartDemand->items;
        $items = collect($items)->filter(function ($item) use ($id) {
            return $item['id'] != $id;
        })->toArray();

        $total_price = collect($items)->sum(function ($i) {
            return $i['price'] * $i['quantity'];
        });

        $cartDemand->update(['items' => $items, 'total_price' => $total_price]);
        return GenericSuccessMessageResponse::getResponse();
    }

    /**
     * Upload order detail product unit image
     *
     * Upload order detail product unit image
     *
     * @param CartDemand $cartDemand
     * @return JsonResponse
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    #[CustomOpenApi\Operation(id: 'CartDemandUploadImage', tags: [Tags::CartDemand, Tags::V1])]
    #[CustomOpenApi\Parameters(model: CartDemand::class)]
    #[OpenApi\RequestBody(factory: ImageRequestBody::class)]
    #[OpenApi\Response(factory: GenericSuccessMessageResponse::class)]
    public function uploadImage(Request $request, CartDemand $cartDemand): JsonResponse
    {
        $request->validate([
            'item_id' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,svg|max:10240'
        ]);
        // if (count($cartDemand->photo) > 0) {
        //     foreach ($cartDemand->photo as $media) {
        //         $media->delete();
        //     }
        // }

        $itemId = $request->item_id;

        $photo = $cartDemand->addMedia($request->file('image'))->toMediaCollection('photo');
        $photoUrl = $photo->original_url;
        $items = $cartDemand->items;
        $newItems = collect($items)->map(function ($item) use ($itemId, $photoUrl) {
            if ($item['id'] == $itemId) {
                $item['image'] = $photoUrl;
            }
            return $item;
        })->toArray();
        $newItems = array_values($newItems);

        $cartDemand->update(['items' => $newItems]);
        return response()->json(['message' => 'success', 'data' => ['cartDemand' => $cartDemand, 'photo' => $photo]]);
    }
}
