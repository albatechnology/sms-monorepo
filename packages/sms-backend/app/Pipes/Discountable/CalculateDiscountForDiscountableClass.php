<?php

namespace App\Pipes\Discountable;

use App\Interfaces\Discountable;
use App\Services\OrderService;
use Closure;
use Exception;

/**
 * Apply discount to the discountable class if applicable
 * Class ResetDiscount
 * @package App\Pipes\Discountable
 */
class CalculateDiscountForDiscountableClass
{
    /**
     * @param Discountable $discountable
     * @param Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle(Discountable $discountable, Closure $next)
    {
        if (!$discount = $discountable->getDiscount()) return $next($discountable);

        if ($discount->applyToOrder()) {
            // $discountable->setSumTotalDiscount(OrderService::calculateTotalDiscount($discountable, $discount));
            $discountable->setTotalDiscount(OrderService::calculateTotalDiscount($discountable, $discount));
            $discountable->setTotalPrice($discountable->getTotalPrice() - $discountable->getTotalDiscount());
        }

        return $next($discountable);
    }
}
