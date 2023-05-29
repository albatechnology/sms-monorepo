<?php

namespace App\Pipes\Discountable;

use App\Enums\DiscountError;
use App\Interfaces\Discountable;
use Closure;

/**
 *
 * Class CheckDiscountUseLimit
 * @package App\Pipes\Discountable
 */
class CheckDiscountUseLimit
{
    public function handle(Discountable $discountable, Closure $next)
    {
        if (!$discount = $discountable->getDiscount()) return $next($discountable);

        if ($discount->customerReachUseLimit($discountable->getCustomerId())) {
            $discountable->resetDiscount();
            $discountable->setDiscountError(DiscountError::USE_LIMIT_REACHED());
            return $discountable;
        }

        return $next($discountable);
    }
}
