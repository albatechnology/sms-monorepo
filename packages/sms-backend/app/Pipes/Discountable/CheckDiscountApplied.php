<?php

namespace App\Pipes\Discountable;

use App\Enums\DiscountError;
use App\Interfaces\Discountable;
use Closure;

/**
 * Check if the discount is applied to any item or order
 *
 * Class CheckDiscountApplied
 * @package App\Pipes\Discountable
 */
class CheckDiscountApplied
{
    public function handle(Discountable $discountable, Closure $next)
    {
        if (!$discount = $discountable->getDiscount()) return $next($discountable);

        if ($discountable->getTotalDiscount() === 0) {
            $discountable->setDiscountError(DiscountError::NOT_APPLICABLE_TO_ANY_PRODUCT());
            $discountable->resetDiscount();
            return $discountable;
        }

        return $next($discountable);
    }
}