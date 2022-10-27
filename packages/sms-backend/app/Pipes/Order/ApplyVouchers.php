<?php

namespace App\Pipes\Order;

use App\Models\Order;
use App\Models\OrderVoucher;
use App\Models\Voucher;
use App\Services\OrderService;
use Closure;

/**
 * Class ApplyVouchers
 * @package App\Pipes\Order
 */
class ApplyVouchers
{
    public function handle(Order $order, Closure $next)
    {
        $order_vouchers = collect([]);
        if (isset($order->raw_source['voucher_ids']) && count($order->raw_source['voucher_ids']) > 0) {


            $order_vouchers = collect($order->raw_source['voucher_ids'])->map(function ($voucher_id) use($order) {
                $voucher = Voucher::findOrFail($voucher_id);
                if($voucher){
                    OrderService::setVoucher($order, $voucher);

                    // if ($voucher = $order->getvoucher()) {
                        $records             = $order->records;
                        $records['vouchers'][] = $voucher->toRecord();
                        $order->records      = $records;
                    // }
                    unset($order->voucher);

                    $order_voucher = new OrderVoucher();
                    $order_voucher->voucher_id = $voucher->id;
                    return $order_voucher;
                } else {
                    return null;
                }
            });

        }

        $order->order_vouchers = $order_vouchers;
        $order->total_voucher = $order->tmp_total_voucher ?? 0;
        unset($order->tmp_total_voucher);
        return $next($order);
    }
}

