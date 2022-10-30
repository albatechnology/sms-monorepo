<?php

namespace App\Pipes\Order\Update;

use App\Models\Order;
use App\Models\Voucher;
use Closure;

/**
 * Class ApplyVouchers
 * @package App\Pipes\Order
 */
class UpdateApplyVouchers
{
    // public function handle(Order $order, Closure $next)
    // {
    //     $order_vouchers = collect([]);
    //     if (isset($order->raw_source['voucher_ids']) && count($order->raw_source['voucher_ids']) > 0) {


    //         $order_vouchers = collect($order->raw_source['voucher_ids'])->map(function ($voucher_id) use($order) {
    //             $voucher = Voucher::findOrFail($voucher_id);
    //             if($voucher){
    //                 OrderService::setVoucher($order, $voucher);

    //                 // if ($voucher = $order->getvoucher()) {
    //                     $records             = $order->records;
    //                     $records['vouchers'][] = $voucher->toRecord();
    //                     $order->records      = $records;
    //                 // }
    //                 unset($order->voucher);

    //                 $order_voucher = new OrderVoucher();
    //                 $order_voucher->voucher_id = $voucher->id;
    //                 return $order_voucher;
    //             } else {
    //                 return null;
    //             }
    //         });

    //     }
    //     // dd($order_vouchers);
    //     $order->order_vouchers = $order_vouchers;
    //     $order->total_voucher = $order->sum_total_voucher ?? 0;
    //     return $next($order);
    // }

    public function handle(Order $order, Closure $next)
    {
        $order->total_voucher = 0;
        if (isset(request()->voucher_ids) && count(request()->voucher_ids) > 0) {
            $vouchers = Voucher::whereHas('customerVouchers', fn ($q) => $q->whereIn('voucher_id', request()->voucher_ids))
                ->whereIsActive()
                // ->whereStartTimeAfter(now())
                // ->whereEndTimeBefore(now())
                ->get();

            $order->order_vouchers = $vouchers;
            $order->total_voucher = $vouchers->sum('value') ?? 0;
            $order->total_price = $order->total_price > $order->total_voucher ? $order->total_price - $order->total_voucher : 0;
        }

        return $next($order);
    }
}
