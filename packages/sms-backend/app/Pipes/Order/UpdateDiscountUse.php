<?php

namespace App\Pipes\Order;

use App\Models\Order;
use App\Services\OrderService;
use Closure;
use Exception;

/**
 * Class UpdateDiscountUse
 * @throws Exception
 * @package App\Pipes\Order
 */
class UpdateDiscountUse
{
    public function handle(Order $order, Closure $next)
    {
        if ($order->getDiscount()) {
            OrderService::recordDiscountUse($order);
        }

        // if ($order->getOrderDiscounts()) {
        //     OrderService::recordDiscountsUse($order);
        // }

        return $next($order);
    }
}
