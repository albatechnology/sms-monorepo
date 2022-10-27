<?php

namespace App\Pipes\Voucherable;

use App\Interfaces\Voucherable;
use App\Interfaces\VoucherableLine;
use Closure;

/**
 * Synchronize sum_total_Voucher to total_Voucher
 *
 * Class SyncSumVoucher
 * @package App\Pipes\Voucherable
 */
class SyncSumVoucher
{
    public function handle(Voucherable $voucherable, Closure $next)
    {
        if (!$voucherable->getVoucher()) return $next($voucherable);

        $voucherable->getVoucherableLines()->each(function (VoucherableLine $line) {
            $line->setTotalVoucher($line->getSumTotalVoucher());
        });

        $voucherable->updatePricesFromItemLine();

        return $next($voucherable);
    }
}
