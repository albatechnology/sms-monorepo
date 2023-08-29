<?php

namespace App\Pipes\Order\Update;

use App\Enums\OrderApprovalStatus;
use App\Models\Order;
use Closure;

/**
 * Class UpdateAdditionalDiscount
 * @package App\Pipes\Order\Update
 */
class UpdateAdditionalDiscount
{
    public function handle(Order $order, Closure $next)
    {
        $additional_discount = (int) request()->additional_discount ?? 0;
        $order->approved_by = null;

        if (isset(request()->discount_take_over_by) && request()->discount_take_over_by != null) {
            $order->additional_discount = $additional_discount;
            return $next($this->updateApprovalDiscount($order));
        }

        if ($additional_discount == 0) {
            $order->approval_status = OrderApprovalStatus::NOT_REQUIRED();
            $order->approved_by = null;
            $order->approval_send_to = null;
            $order->approval_supervisor_type_id = null;
            $order->additional_discount = 0;
            $order->additional_discount_ratio = null;
            return $next($order);
        }

        if (isset(request()->discount_type) && request()->discount_type == 1) {
            $additional_discount = ($additional_discount / 100) * $order->total_price;
            $order->total_price -= $additional_discount;
        } else {
            $order->total_price -= $additional_discount;
        }

        // $order->discount_take_over_by = auth()->id();
        // $order->additional_discount = $additional_discount;
        // $order->approval_send_to = 3; // send to supervisor
        // $order->approval_status = OrderApprovalStatus::WAITING_APPROVAL();
        // $order->additional_discount_ratio = $this->calculateOrderAdditionalDiscountRatio($order);

        $user = auth()->user();

        $order->discount_take_over_by = $user->id;
        $order->additional_discount = $additional_discount;
        $order->approval_send_to = 3; // send to user type supervisor
        $order->approval_status = OrderApprovalStatus::WAITING_APPROVAL();
        $order->additional_discount_ratio = $this->calculateOrderAdditionalDiscountRatio($order);

        // $productBrandIds = $order->raw_source['product_brand_ids'];

        if ($user->is_supervisor) {
            $supervisor = $user;
        } else {
            $supervisor = $user->supervisor;
        }

        if ($supervisor && ($supervisor->getLimitApproval() >= $order->additional_discount_ratio)) {
            $order->approval_supervisor_type_id = $supervisor->supervisor_type_id;
        } else {
            $order->approval_supervisor_type_id = 2;
        }

        // if (count($productBrandIds) > 1) {
        //     $additional_discount_ratio = $order->additional_discount_ratio;
        //     $storeLeaderLimitApprovals = $storeLeader->supervisorApprovalLimits->whereIn('product_brand_id', $productBrandIds)->filter(function ($data) use ($additional_discount_ratio) {
        //         return $data?->limit ?? 0 >= $additional_discount_ratio;
        //     });

        //     if (count($storeLeaderLimitApprovals) > 0) {
        //         $approval_supervisor_type_id = 1;
        //     } else {
        //         $approval_supervisor_type_id = 2;
        //     }
        // } else {
        //     $productBrandId = $productBrandIds[0];
        //     $storeLeaderLimitApproval = $storeLeader->supervisorApprovalLimits->first(fn ($data) => $data->product_brand_id == $productBrandId);
        //     $approval_supervisor_type_id = 1;

        //     if ($order->additional_discount_ratio > $storeLeaderLimitApproval?->limit ?? 0) {
        //         $approval_supervisor_type_id = 2;
        //     }
        // }

        // $order->approval_supervisor_type_id = $approval_supervisor_type_id;



        return $next($order);
    }

    public function updateApprovalDiscount(Order $order)
    {
        $user = auth()->user();
        if (isset(request()->discount_type) && request()->discount_type == 1) {
            $order->additional_discount = ($order->additional_discount / 100) * $order->total_price;
            $order->total_price -= $order->additional_discount;
        } else {
            $order->total_price -= $order->additional_discount;
        }

        if ($user->is_supervisor && ($user->getLimitApproval() >= $order->additional_discount_ratio)) {
            $order->approved_by = $user->id;
            $order->approval_status = OrderApprovalStatus::APPROVED();
        } else {
            $order->approval_status = OrderApprovalStatus::WAITING_APPROVAL();
        }

        $order->approval_note = request()->approval_note ?? null;
        $order->approval_send_to = $user->supervisor_type_id == 1 ? 3 : 4; // send to supervisor or director
        $order->additional_discount_ratio = $order->additional_discount == 0 ? null : $this->calculateOrderAdditionalDiscountRatio($order);
        $order->approval_supervisor_type_id = $order->approval_send_to == 4 ? null : 2; // if director fill null

        return $order;

        // if ($user->is_supervisor) {
        //     $productBrandIds = $order->raw_source['product_brand_ids'];

        //     if (count($productBrandIds) > 1) {
        //         $additional_discount_ratio = $order->additional_discount_ratio;
        //         $userLimitApprovals = $user->supervisorApprovalLimits->whereIn('product_brand_id', $productBrandIds)->filter(function ($data) use ($additional_discount_ratio) {
        //             return $data?->limit ?? 0 >= $additional_discount_ratio;
        //         });

        //         if (count($userLimitApprovals) > 0) {
        //             $order->approved_by = $user->id;
        //             $order->approval_status = OrderApprovalStatus::APPROVED();
        //         } else {
        //             $order->approval_status = OrderApprovalStatus::WAITING_APPROVAL();
        //         }
        //     } else {
        //         $productBrandId = $productBrandIds[0];
        //         $userLimitApproval = $user->supervisorApprovalLimits->first(fn ($data) => $data->product_brand_id == $productBrandId);
        //         if ($order->additional_discount <= $userLimitApproval?->limit ?? 0) {
        //             $order->approved_by = $user->id;
        //             $order->approval_status = OrderApprovalStatus::APPROVED();
        //         } else {
        //             $order->approval_status = OrderApprovalStatus::WAITING_APPROVAL();
        //         }
        //     }
        // } else {
        //     $order->approval_status = OrderApprovalStatus::WAITING_APPROVAL();
        // }

        // $approval_send_to = $user->supervisor_type_id == 1 ? 3 : 4;
        // $order->approval_note = request()->approval_note ?? null;
        // $order->approval_send_to = $approval_send_to; // send to supervisor or director
        // $order->additional_discount_ratio = $order->additional_discount == 0 ? null : $this->calculateOrderAdditionalDiscountRatio($order);
        // $order->approval_supervisor_type_id = $approval_send_to == 4 ? null : 2; // if director fill null

        // return $order;
    }

    public function calculateOrderAdditionalDiscountRatio(Order $order): ?int
    {
        if (is_null($order->additional_discount)) {
            return null;
        }

        // get ratio as percentage
        $ratio = ($order->total_price + $order->additional_discount) * $order->additional_discount == 0 ? 0 : round(100 / ($order->total_price + $order->additional_discount) * $order->additional_discount);

        if ($ratio === $order->additional_discount_ratio) {
            return $ratio;
        }

        $order->additional_discount_ratio = (int) $ratio;

        return $ratio;
    }
}
