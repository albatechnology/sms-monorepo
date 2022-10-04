<?php

namespace App\Pipes\Discountable;

use App\Enums\DiscountError;
use App\Interfaces\Discountable;
use Closure;

/**
 * Check if discount is currently still active.
 *
 * Class CheckDiscountActive
 * @package App\Pipes\Discountable
 */
class CheckDiscountActive
{
    public function handle(Discountable $discountable, Closure $next)
    {
        if (!$discountable->getDiscount()) return $next($discountable);

        if (!$discountable->getDiscount()->isActiveNow()) {
            $discountable->resetDiscount();
            $discountable->setDiscountError(DiscountError::INACTIVE());
            return $discountable;
        }

        return $next($discountable);
    }
}
