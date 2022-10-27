<?php

namespace App\Pipes\Voucherable;

use App\Enums\VoucherError;
use App\Interfaces\Voucherable;
use Closure;

/**
 * Check if Voucher is currently still active.
 *
 * Class CheckVoucherActive
 * @package App\Pipes\Voucherable
 */
class CheckVoucherActive
{
    public function handle(Voucherable $voucherable, Closure $next)
    {
        if (!$voucherable->getVoucher()) return $next($voucherable);

        if (!$voucherable->getVoucher()->isActiveNow()) {
            $voucherable->resetVoucher();
            $voucherable->setVoucherError(VoucherError::INACTIVE());
            return $voucherable;
        }
        return $next($voucherable);
    }
}
