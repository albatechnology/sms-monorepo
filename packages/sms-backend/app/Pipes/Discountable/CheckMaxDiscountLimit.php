<?php

namespace App\Pipes\Discountable;

use App\Interfaces\Discountable;
use Closure;

/**
 *
 * Class CheckMaxDiscountLimit
 * @package App\Pipes\Discountable
 */
class CheckMaxDiscountLimit
{
    public function handle(Discountable $discountable, Closure $next)
    {
        if (!$discount = $discountable->getDiscount()) return $next($discountable);

        $max_discount = $discount->max_discount_price_per_order;

        if ($max_discount && $max_discount < $discountable->getTotalDiscount()) {
            $discountable->resetDiscountPrices();

            // apply the discount cap
            $discountable->setTotalDiscount(min($max_discount, $discountable->getTotalPrice()));
            $discountable->setTotalPrice($discountable->getTotalPrice() - $discountable->getTotalDiscount());
        }

        return $next($discountable);
    }
}