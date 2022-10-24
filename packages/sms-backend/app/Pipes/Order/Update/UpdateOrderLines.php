<?php

namespace App\Pipes\Order\Update;

use App\Models\Order;
use App\Models\OrderDetail;
use Closure;

class UpdateOrderLines
{
    public function handle(Order $order, Closure $next)
    {
        $order_details = collect();
        if ($order->order_details && count($order->order_details) > 0) {
            $order_details = $order->order_details->map(function (OrderDetail $detail) {
                $detail->total_cascaded_discount = 0;
                $detail->sum_total_discount  = 0; // will be unset before save
                $detail->total_discount = 0;
                $detail->total_price = $detail->unit_price * $detail->quantity;

                return $detail;
            });
        }

        $order->order_details = $order_details;
        $order->total_price = $order->order_details->sum(fn (OrderDetail $detail) => $detail->total_price);

        return $next($order);
    }
}
