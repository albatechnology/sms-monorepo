<?php

namespace App\Pipes\Order\Admin\CartDemand;

// use App\Enums\OrderApprovalStatus;
use App\Models\Order;
// use App\Services\OrderService;
use Closure;

/**
 * Class AddAdditionalDiscount
 * @package App\Pipes\Order
 */
class AddAdditionalDiscount
{
    public function handle(Order $order, Closure $next)
    {
        if (empty($order->additional_discount) || $order->additional_discount == 0) {
            return $next($order);
        }
        $order->total_price -= $order->additional_discount;

        // will require approval unless explicitly stated otherwise
        // if(isset($order->raw_source['require_approval']) && $order->raw_source['require_approval'] === false){
        //     return $next($order);
        // }

        // $order->approval_status = OrderApprovalStatus::WAITING_APPROVAL();
        // $order->additional_discount_ratio = app(OrderService::class)->calculateOrderAdditionalDiscountRatio($order);

        // get ratio as percentage
        $ratio = ($order->total_price + $order->additional_discount) * $order->additional_discount == 0 ? 0 : round(100 / ($order->total_price + $order->additional_discount) * $order->additional_discount);
        $order->additional_discount_ratio = $ratio;

        return $next($order);
    }
}
