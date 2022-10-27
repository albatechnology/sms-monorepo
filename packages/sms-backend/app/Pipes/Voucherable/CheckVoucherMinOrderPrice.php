<?php

namespace App\Pipes\Voucherable;

use App\Enums\VoucherError;
use App\Interfaces\Voucherable;
use Closure;

/**
 *
 * Class CheckVoucherMinOrderPrice
 * @package App\Pipes\Voucherable
 */
class CheckVoucherMinOrderPrice
{
    public function handle(Voucherable $voucherable, Closure $next)
    {
        if (!$voucher = $voucherable->getVoucher()) return $next($voucherable);

        if (!is_null($voucher->min_order_price) && $voucherable->getTotalPriceVoucher() < $voucher->min_order_price) {
            $voucherable->resetVoucher();
            $voucherable->setVoucherError(VoucherError::UNDER_MINIMUM_PRICE());
            return $voucherable;
        }
        return $next($voucherable);
    }
}
