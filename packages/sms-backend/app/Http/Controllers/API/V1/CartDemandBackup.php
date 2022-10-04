<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\DocGenerator\Enums\Tags;
use App\Http\Requests\API\V1\CartDemand\SyncCartDemandRequest;
use App\Http\Resources\V1\CartDemand\CartDemandResource;
use App\Models\Cart;
use App\Models\CartDemand;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use App\OpenApi\Responses\Custom\GenericSuccessMessageResponse;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;


#[OpenApi\PathItem]
class CartDemandBackup extends BaseApiController
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
        if(!$cartDemand) {
            $cartDemand = new CartDemand;
            $cartDemand->user_id = $user->id;
            $items = $requestItems;
        } else {
            $cartDemandItems = json_decode($cartDemand->items, true);
            $items = array_merge($cartDemandItems, $requestItems);
        }

        $total_price = collect($items)->sum(function($i){
            return $i['price'] * $i['quantity'];
        });
        $items = collect($items)->map(function($item, $id){
            $item['id'] = $id + 1;
            return $item;
        })->toArray();

        $cartDemand->total_price = $total_price;
        $cartDemand->items = json_encode($items);
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
        $items = json_decode($cartDemand->items, true);
        $items = collect($items)->filter(function ($item) use($id) {
            return $item['id'] != $id;
        })->toArray();

        $total_price = collect($items)->sum(function($i){
            return $i['price'] * $i['quantity'];
        });

        $cartDemand->update(['items' => json_encode($items), 'total_price' => $total_price]);
        return GenericSuccessMessageResponse::getResponse();
    }
}
