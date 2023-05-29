<?php

namespace App\Pipes\Discountable;

use App\Interfaces\Discountable;
use App\Services\OrderService;
use Closure;

/**
 * If discount applied on order level, calculate the cascaded discount
 * for each order line
 *
 * Class CalculateDiscountCascadeForDiscountableLine
 * @package App\Pipes\Discountable
 */
class CalculateDiscountCascadeForDiscountableLine
{
    public function handle(Discountable $discountable, Closure $next)
    {
        // moved method to service as this is reused in migration to fix old data
        // $discountable->tmp_total_discount = $discountable->getTotalDiscount();
        app(OrderService::class)->setOrderDetailCascadedDiscount($discountable);

        return $next($discountable);
    }
}
