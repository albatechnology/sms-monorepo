<?php

namespace App\Pipes\Order;

use App\Enums\OrderDetailStatus;
use App\Models\CartDemand;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderDetailDemand;
use App\Models\Product;
use Closure;

class MakeOrderLinesBackup
{
    public function handle(Order $order, Closure $next)
    {
        // Starts by grabbing all the product unit model
        $items = collect($order->raw_source['items']);
        $units = Product::whereIn('id', $items->pluck('id'))
            ->with(['product', 'colour', 'covering'])
            ->get()
            ->keyBy('id');

        $order_details = $items->map(function ($data) use ($units) {

            /** @var Product $product_unit */
            $product_unit = $units[$data['id']];

            $order_detail             = new OrderDetail();
            $order_detail->status     = OrderDetailStatus::NOT_FULFILLED();
            $order_detail->company_id = user()->company_id;

            // We do not bother with stock fulfilment and discount
            // calculation yet at this stage
            $order_detail->records         = [
                'product_unit' => $product_unit->toRecord(),
                'product'      => $product_unit->product->toRecord(),
                'images'       => $product_unit->product->version->getRecordImages()
            ];
            $order_detail->quantity        = (int)$data['quantity'];
            $order_detail->product_unit_id = $product_unit->id;
            $order_detail->unit_price      = $product_unit->price;
            $order_detail->total_discount  = 0;
            $order_detail->total_price     = $product_unit->price * $data['quantity'];

            return $order_detail;
        });

        $order->order_details = $order_details;
        $order->total_price   = $order_details->sum(fn (OrderDetail $detail) => $detail->total_price);

        $cartDemand = CartDemand::where('user_id', $order->user_id)->first();
        if ($cartDemand && $cartDemand->items != '') {
            $items = json_decode($cartDemand->items, true);
            $order_detail_demands = collect($items)->map(function ($data) {
                $order_detail_demand             = new OrderDetailDemand();
                $order_detail_demand->status     = OrderDetailStatus::NOT_FULFILLED();
                $order_detail_demand->company_id = user()->company_id;

                $order_detail_demand->quantity        = (int)$data['quantity'];

                $order_detail_demand->unit_price      = (int)$data['price'];
                $order_detail_demand->total_price     = $data['price'] * $order_detail_demand->quantity;

                return $order_detail_demand;
            });

            $order->order_detail_demands = $order_detail_demands;
        }

        return $next($order);
    }
}
