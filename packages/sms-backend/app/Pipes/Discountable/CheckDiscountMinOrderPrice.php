<?php

namespace App\Pipes\Discountable;

use App\Enums\DiscountError;
use App\Interfaces\Discountable;
use Closure;

/**
 *
 * Class CheckDiscountMinOrderPrice
 * @package App\Pipes\Discountable
 */
class CheckDiscountMinOrderPrice
{
    public function handle(Discountable $discountable, Closure $next)
    {
        if (!$discount = $discountable->getDiscount()) return $next($discountable);

        if (!is_null($discount->min_order_price) && $discountable->getTotalPrice() < $discount->min_order_price) {
            $discountable->resetDiscount();
            $discountable->setDiscountError(DiscountError::UNDER_MINIMUM_PRICE());
            return $discountable;
        }

        return $next($discountable);
    }
}
