<?php

namespace App\Pipes\Order;

use App\Exceptions\ExpectedOrderPriceMismatchException;
use App\Models\Order;
use Closure;

/**
 * Class CheckExpectedOrderPrice
 * @package App\Pipes\Order
 */
class CheckExpectedOrderPrice
{
    public function handle(Order $order, Closure $next)
    {
        if (!$order->expected_price) return $next($order);

        if ($order->total_price != $order->expected_price) {
            throw new ExpectedOrderPriceMismatchException();
        }

        return $next($order);
    }
}
