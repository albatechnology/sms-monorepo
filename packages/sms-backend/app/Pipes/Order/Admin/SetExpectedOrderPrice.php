<?php

namespace App\Pipes\Order\Admin;

use App\Models\Order;
use Closure;

/**
 * Class CheckExpectedOrderPrice
 * @package App\Pipes\Order\Admin
 */
class SetExpectedOrderPrice
{
    public function handle(Order $order, Closure $next)
    {
        $order->expected_price = $order->total_price;

        return $next($order);
    }
}
