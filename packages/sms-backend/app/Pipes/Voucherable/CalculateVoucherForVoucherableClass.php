<?php

namespace App\Pipes\Voucherable;

use App\Interfaces\Voucherable;
use App\Services\OrderService;
use Closure;
use Exception;

/**
 * Apply Voucher to the Voucherable class if applicable
 * Class ResetVoucher
 * @package App\Pipes\Voucherable
 */
class CalculateVoucherForVoucherableClass
{
    /**
     * @param Voucherable $voucherable
     * @param Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle(Voucherable $voucherable, Closure $next)
    {
        if (!$voucher = $voucherable->getVoucher()) return $next($voucherable);

        // if ($voucher->applyToOrder()) {
            $voucherable->setSumTotalVoucher(OrderService::calculateTotalVoucher($voucherable, $voucher));
            $voucherable->setTotalVoucher(OrderService::calculateTotalVoucher($voucherable, $voucher, false));
            $voucherable->setTotalPriceVoucher($voucherable->getTotalPriceVoucher() - $voucherable->getTotalVoucher());
        // }
        // dump('CalculateVoucherForVoucherableClass');

        return $next($voucherable);
    }
}
