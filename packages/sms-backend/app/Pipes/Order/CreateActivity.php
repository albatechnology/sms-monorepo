<?php

namespace App\Pipes\Order;

use App\Models\Activity;
use App\Models\ActivityBrandValue;
use App\Models\Order;
use Closure;

/**
 * Create activity for given order
 * then update order_value in activity_brand_values
 * and unset activity_datas from order data
 *
 * Class CreateActivity
 * @package App\Pipes\Order
 */
class CreateActivity
{
    // public function handle(Order $order, Closure $next)
    // {
    //     $lastActivity = Activity::whereNull('order_id')->where('lead_id', $order->lead_id)->where('customer_id', $order->customer_id)->where('channel_id', $order->channel_id)->first();

    //     $lastActivityId = $lastActivity?->id && !$lastActivity->child ? $lastActivity->id : null;

    //     $activity = Activity::createForOrder($order, $lastActivityId);

    //     $activityId = $lastActivityId == null ? $activity->id : $lastActivityId;
    //     foreach ($order->activity_datas as $product_brand_id => $value) {
    //         ActivityProductBrand::updateOrCreate([
    //             'activity_id' => $activityId,
    //             'product_brand_id' => $product_brand_id,
    //         ], [
    //             'order_value' => $value
    //         ]);
    //     }

    //     unset($order->activity_datas);

    //     return $next($order);
    // }

    public function handle(Order $order, Closure $next)
    {
        $activity = Activity::createForOrder($order);
        $order->lead->update(['last_activity_status', 1]);

        $additionalDiscount = (int)$order->additional_discount ?? 0;
        $sumActivityDatas = $order->activity_datas->sum();
        foreach ($order->activity_datas as $product_brand_id => $value) {
            // ActivityBrandValue::updateOrCreate([
            //     'user_id' => $activity->user_id,
            //     'lead_id' => $activity->lead_id,
            //     'product_brand_id' => $product_brand_id,
            // ], [
            //     'order_value' => $value,
            //     'activity_id' => $activity->id,
            // ]);

            $totalDiscount = (($value / $sumActivityDatas) * $additionalDiscount) ?? 0;
            ActivityBrandValue::create([
                'user_id' => $activity->user_id,
                'activity_id' => $activity->id,
                'lead_id' => $activity->lead_id,
                'product_brand_id' => $product_brand_id,
                'order_value' => $value,
                'total_discount' => $totalDiscount,
                'total_order_value' => $value - $totalDiscount,
                'order_id' => $order->id
            ]);
        }

        // ActivityBrandValue::where('user_id', $activity->user_id)->where('lead_id', $activity->lead_id)->update(['order_id' => $order->id]);
        unset($order->activity_datas);

        return $next($order);
    }
}
