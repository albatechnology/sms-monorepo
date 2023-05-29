<?php

namespace App\Pipes\Order;

// use App\Models\CartDemand;
use App\Models\Order;
use Closure;
use Illuminate\Support\Facades\DB;

/**
 * Save order, order detail, and activity
 *
 * Class SaveOrder
 * @package App\Pipes\Order
 */
class SaveOrder
{
    public function handle(Order $order, Closure $next)
    {
        $order = DB::transaction(function () use ($order) {
            $details = $order->order_details;
            // $order_discounts = $order->order_discounts ?? collect([]);
            // $order_vouchers = $order->order_vouchers ?? collect([]);
            unset($order->order_details);
            // unset($order->order_discounts);
            // unset($order->order_vouchers);
            unset($order->discount);
            unset($order->allowed_product_ids);
            unset($order->sum_total_discount);

            // collect($details)->each(function (OrderDetail $detail) use($activityDatas) {
            //     $activityDatas[] = [$detail->product_brand_id => $detail->total_price];
            //     unset($detail->discount);
            //     unset($detail->discount_id);
            //     unset($detail->product_brand_id);
            // });

            $activityDatas = [];
            foreach ($details as $detail) {
                $activityDatas[] = [$detail->product_brand_id => $detail->total_price];
                unset($detail->discount);
                unset($detail->discount_id);
                unset($detail->product_brand_id);
                unset($detail->sum_total_discount);
            }

            $order->save();
            $order->order_details()->saveMany($details);
            // if ($order_discounts->count() > 0) $order->order_discounts()->saveMany($order_discounts);
            // if ($order_vouchers->count() > 0) $this->applyVouchers($order, $order_vouchers);

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
        $customer = $order->customer;
        foreach ($vouchers as $voucher) {
            $order->orderVouchers()->create(['voucher_id' => $voucher->id]);
            $customer->vouchers()->updateExistingPivot($voucher->id, [
                'is_used' => true,
            ]);
        }
    }
}
