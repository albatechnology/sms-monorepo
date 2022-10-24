<?php

namespace App\Pipes\Discountable;

use App\Interfaces\Discountable;
use App\Interfaces\DiscountableLine;
use Closure;

/**
 * Synchronize sum_total_discount to total_discount
 *
 * Class SyncSumDiscount
 * @package App\Pipes\Discountable
 */
class SyncSumDiscount
{
    public function handle(Discountable $discountable, Closure $next)
    {
        if (!$discountable->getDiscount()) return $next($discountable);

        $discountable->getDiscountableLines()->each(function (DiscountableLine $line) {
            $line->setTotalDiscount($line->getSumTotalDiscount());
        });

        $discountable->updatePricesFromItemLine();

        return $next($discountable);
    }
}
