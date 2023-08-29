<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CartItem;
use App\Classes\DocGenerator\Enums\Tags;
use App\Enums\StockTransferStatus;
use App\Http\Requests\API\V1\Cart\SyncCartRequest;
use App\Http\Resources\V1\Cart\CartResource;
use App\Models\Cart;
use App\Models\Stock;
use App\Models\StockTransfer;
use App\Models\Channel;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use Illuminate\Http\Request;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class CartController extends BaseApiController
{
    const load_relation = [];

    /**
     * Show user cart.
     *
     * Show cart of logged in user
     */
    #[CustomOpenApi\Operation(id: 'CartIndex', tags: [Tags::Cart, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: CartResource::class, statusCode: 200)]
    public function index()
    {
        return new CartResource(tenancy()->getUser()->cart ?? new Cart());
    }

    /**
     * Sync cart
     *
     * Sync user cart content
     *
     * @param SyncCartRequest $request
     * @return CartResource
     */
    #[CustomOpenApi\Operation(id: 'CartSync', tags: [Tags::Cart, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: SyncCartRequest::class)]
    #[CustomOpenApi\Response(resource: CartResource::class, statusCode: 200)]
    public function sync(SyncCartRequest $request): CartResource
    {
        $cart = tenancy()
            ->getUser()
            ->syncCart(
                CartItem::fromRequest($request),
                $request->customer_id ?? null,
                $request->discount_id ?? null,
            );

        return new CartResource($cart);
    }

    public function stockIndex(Request $request, $productId)
    {
        // return CustomQueryBuilder::buildResource(
        //     Channel::class,
        //     CartChannelStockResource::class,
        //     fn($query) => $query->stocks(Stock::where('product_id', $productId)->get()->pluck('channel_id')->toArray())
        //       ->withSum('channelStocks', 'stock', function($q) use($productId) {
        //         $q->where('product_id', $productId);
        //       }),
        //     null,
        //     0
        // );

        $user = tenancy()->getUser();
        $cartQuantity = $user->cart->item_lines->sum('quantity');

        $stockIds = Stock::where('product_id', $productId)->get()->pluck('channel_id')->toArray();

        $currentChannel = Channel::find($user->channel_id);
        $channels = Channel::where(function ($q) use ($currentChannel, $stockIds) {
            $q->whereNotIn('id', [$currentChannel->id]);
            $q->whereIn('id', $stockIds);
        })->withSum('channelStocks', 'stock', function ($q) use ($productId) {
            $q->where('product_id', $productId);
        })->orderBy('id', 'asc')
            ->get();

        $currentChannelStock = (int)$currentChannel->withSum('channelStocks', 'stock', function ($q) use ($productId) {
            $q->where('product_id', $productId);
        })->first()->channel_stocks_sum_stock;

        $unfilledCurrentChannelStockQuantity = ($currentChannelStock - $cartQuantity) > 0 ? 0 : abs($currentChannelStock - $cartQuantity);

        $currentChannel = [
            'id' => $currentChannel->id,
            'name' => $currentChannel->name,
            'available_quantity' => $currentChannelStock,
            'cart_quantity' => $cartQuantity
        ];

        $otherChannels = [];
        $filledQuantity = 0;

        foreach ($channels as $channel) {
            if (in_array($channel->id, $user->cart->stockTransfers->pluck('from_channel_id')->toArray())) {
                $channelCartQuantity = $user->cart->stockTransfers()
                    ->where('from_channel_id', $channel->id)
                    ->where('product_id', $productId)
                    ->where('status', StockTransferStatus::PENDING)
                    ->first()->amount ?? 0;
            } else {
                $channelCartQuantity = 0;
            }

            $filledQuantity += $channelCartQuantity;

            array_push($otherChannels, [
                'id' => $channel->id,
                'name' => $channel->name,
                'available_quantity' => (int)$channel->channel_stocks_sum_stock,
                'cart_quantity' => $channelCartQuantity
            ]);
        }

        $filledQuantity = $unfilledCurrentChannelStockQuantity == 0 ? $cartQuantity : ($currentChannelStock + $filledQuantity);
        $unfilledQuantity = $cartQuantity - $filledQuantity;

        return response()->json([
            'current_channels' => $currentChannel,
            'other_channels' => $otherChannels,
            'cart_quantity' => $cartQuantity,
            'filled_quantity' => $filledQuantity,
            'unfilled_quantity' => $unfilledQuantity
        ]);
    }

    public function stockSync(Request $request)
    {
        $stockTransfer = StockTransfer::where(function ($q) use ($request) {
            $q->where('cart_id', tenancy()->getUser()->cart->id);
            $q->where('product_id', $request->product_id);
        })->forceDelete();

        foreach ($request->stocks as $stock) {
            if ($stock['amount'] > 0) {
                StockTransfer::create([
                    'cart_id' => tenancy()->getUser()->cart->id,
                    'from_channel_id' => $stock['channel_id'],
                    'product_id' => $request->product_id,
                    // 'company_id' => tenancy()->getUser()->company_id,
                    'to_channel_id' => tenancy()->getUser()->channel_id,
                    'amount' => $stock['amount'],
                    'status' => StockTransferStatus::PENDING
                ]);
            }
        }

        return response()->json('success');
    }
}
