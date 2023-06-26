<?php

namespace App\Pipes\Order;

use App\Enums\OrderDetailStatus;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Closure;

class MakeOrderLines
{
    public function handle(Order $order, Closure $next)
    {
        $order_details = collect([]);

        if (isset($order->raw_source['items']) && count($order->raw_source['items']) > 0) {
            // Starts by grabbing all the product unit model
            $items = collect($order->raw_source['items']);
            $units = Product::whereIn('id', $items->pluck('id'))
                // ->with(['product', 'colour', 'covering'])
                ->get()
                ->keyBy('id');

            $order_details = $items->map(function ($data) use ($units) {

                /** @var Product $product */
                $product = $units[$data['id']];

                $order_detail             = new OrderDetail();
                $order_detail->status     = OrderDetailStatus::NOT_FULFILLED();
                // $order_detail->company_id = user()->company_id;

                // We do not bother with stock fulfilment and discount
                // calculation yet at this stage
                $order_detail->records         = [
                    'product' => $product->toRecord(),
                    // 'product'      => $product->product->toRecord(),
                    // 'images'       => $product->product->version->getRecordImages()
                    'images'       => $product->getRecordImages()
                ];
                $order_detail->quantity        = (int)$data['quantity'];
                $order_detail->product_id = $product->id;
                $order_detail->unit_price      = $product->price;
                $order_detail->sum_total_discount  = 0; // will be unset before save
                $order_detail->total_discount  = 0;
                $order_detail->total_price     = $product->price * $data['quantity'];

                // $order_detail->product_brand_id = $product->product->product_brand_id;
                $order_detail->product_brand_id = $product->product_brand_id;
                // $order_detail->is_ready = $data['is_ready'] ?? 1;

                return $order_detail;
            });
        }

        $productBrandIds = $order_details->pluck('product_brand_id')->unique()->all();
        $rawSource = $order->raw_source;
        $rawSource['product_brand_ids'] = $productBrandIds;
        $order->raw_source = $rawSource;

        $order->order_details = $order_details;
        $order->total_price   = $order_details->sum(fn (OrderDetail $detail) => $detail->total_price);

        return $next($order);
    }
}
