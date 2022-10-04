<?php

namespace App\Pipes\Order\Admin\CartDemand;

use App\Models\Discount;
use App\Models\Order;
use App\Services\OrderService;
use Closure;

/**
 * Apply discount on order and order detail level.
 *
 * Class ApplyDiscount
 * @package App\Pipes\Order\Update
 */
class UpdateApplyDiscount
{
    public function handle(Order $order, Closure $next)
    {
        if ($order->discount_id == null) return $next($order);

        $discount = Discount::find($order->discount_id);
        if (!$discount) return $next($order);
        OrderService::setDiscount($order, $discount);
        if ($discount = $order->getDiscount()) {
            $records             = $order->records;
            $records['discount'] = $discount->toRecord();
            $order->records      = $records;
        }

        return $next($order);
    }
}
