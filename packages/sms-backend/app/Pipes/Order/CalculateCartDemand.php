<?php

namespace App\Pipes\Order;

use App\Models\Order;
use Closure;

/**
 * Class CalculateCartDemand
 * @package App\Pipes\Order
 */
class CalculateCartDemand
{
    public function handle(Order $order, Closure $next)
    {
        $order->total_price += \App\Services\OrderService::calculateCartDemand($order->user_id, $order->id);

        return $next($order);
    }
}
