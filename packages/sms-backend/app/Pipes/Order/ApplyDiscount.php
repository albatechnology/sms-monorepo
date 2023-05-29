<?php

namespace App\Pipes\Order;

use App\Models\Discount;
use App\Models\Order;
// use App\Models\OrderDiscount;
use App\Services\OrderService;
use Closure;

/**
 * Apply discount on order and order detail level.
 *
 * Class ApplyDiscount
 * @package App\Pipes\Order
 */
class ApplyDiscount
{
    public function handle(Order $order, Closure $next)
    {
        if (!$discount_id = $order->raw_source['discount_id'] ?? null) return $next($order);
        $discount = Discount::find($discount_id);
        if ($discount) {
            OrderService::setDiscount($order, $discount);

            // if ($discount = $order->getDiscount()) {
            $records             = $order->records;
            // $records['discounts'][] = $discount->toRecord();
            $records['discount'] = $discount->toRecord();
            $order->records      = $records;
            // }

            unset($order->discount);
        }
        // $order_discounts = collect([]);
        // if (isset($order->raw_source['discount_ids']) && count($order->raw_source['discount_ids']) > 0) {


        //     $order_discounts = collect($order->raw_source['discount_ids'])->map(function ($discount_id) use($order) {
        //         $discount = Discount::findOrFail($discount_id);
        //         if($discount){
        //             OrderService::setDiscount($order, $discount);

        //             // if ($discount = $order->getDiscount()) {
        //                 $records             = $order->records;
        //                 $records['discounts'][] = $discount->toRecord();
        //                 $order->records      = $records;
        //             // }
        //             unset($order->discount);

        //             $order_discount = new OrderDiscount();
        //             $order_discount->discount_id = $discount->id;
        //             return $order_discount;
        //         } else {
        //             return null;
        //         }
        //     });

        // }

        // $order->order_discounts = $order_discounts;
        // $order->total_discount = $order->tmp_total_discount ?? 0;
        // unset($order->tmp_total_discount);
        return $next($order);
    }
}
