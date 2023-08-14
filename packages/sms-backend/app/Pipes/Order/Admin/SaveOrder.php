<?php

namespace App\Pipes\Order\Admin;

use App\Models\Order;
use Closure;
use Illuminate\Support\Facades\DB;

/**
 * Save order, order detail, and activity
 *
 * Class SaveOrder
 * @package App\Pipes\Order\Admin
 */
class SaveOrder
{
    public function handle(Order $order, Closure $next)
    {
        $order = DB::transaction(function () use ($order) {
            $details = $order->order_details;
            $order_discounts = $order->order_discounts ?? collect([]);
            $order_vouchers = $order->order_vouchers ?? collect([]);
            unset($order->order_details);
            unset($order->order_discounts);
            unset($order->order_vouchers);
            unset($order->discount);
            unset($order->allowed_product_ids);
            unset($order->sum_total_discount);

            $order->save();

            $activityDatas = [];
            foreach ($details as $detail) {
                $activityDatas[] = [$detail->product_brand_id => $detail->total_price];
                unset($detail->discount);
                unset($detail->discount_id);
                unset($detail->product_brand_id);
                unset($detail->sum_total_discount);
                $detail->order_id = $order->id;
                $detail->save();
            }

            if ($order_discounts->count() > 0) $order->order_discounts()->saveMany($order_discounts);
            // $this->applyVouchers($order, $order_vouchers);

            // $cartDemand = CartDemand::where('user_id', $order->user_id)->whereNotNull('items')->whereNotOrdered()->first();
            // if ($cartDemand) $cartDemand->update(['order_id' => $order->id, 'created_at' => $order->created_at]);

            // for calculate in CreateActivity class
            $activityDatas = collect($activityDatas)
                ->groupBy(function ($item) {
                    return collect($item)->keys()->first();
                })
                ->map(function ($items) {
                    return collect($items)->flatten()->sum();
                });
            $order->activity_datas = $activityDatas;
            // for calculate in CreateActivity class

            return $order;
        });

        return $next($order);
    }

    private function applyVouchers(Order $order, $vouchers)
    {
        $orderVouchers = $order->orderVouchers;
        $customer = $order->customer;

        if ($orderVouchers->count() > 0) $order->orderVouchers()->delete();

        $customer->vouchers()->where('customer_id', $customer->id)->whereIn('voucher_id', $orderVouchers?->pluck('voucher_id') ?? [])->update(['is_used' => 0]);

        if ($vouchers->count() > 0) {
            foreach ($vouchers as $voucher) {
                $order->orderVouchers()->create(['voucher_id' => $voucher->id]);
                $customer->vouchers()->updateExistingPivot($voucher->id, [
                    'is_used' => true,
                ]);
            }
        }
    }
}
