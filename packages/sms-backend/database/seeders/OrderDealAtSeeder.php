<?php

namespace Database\Seeders;

use App\Enums\OrderPaymentStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Database\Seeder;

class OrderDealAtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $paymentStatuses = [
            OrderPaymentStatus::DOWN_PAYMENT,
            OrderPaymentStatus::SETTLEMENT,
            OrderPaymentStatus::OVERPAYMENT,
        ];

        $orders = Order::query()
            ->whereIn('payment_status', $paymentStatuses)
            ->whereNull('deal_at')
            ->get();

        $orders->each(function (Order $order) {
            // use first approved payment as the time
            $payment = $order->orderPayments()
                ->where('status', PaymentStatus::APPROVED)
                ->orderBy('updated_at', 'ASC')
                ->first();

            if (!$payment) {
                return;
            }

            $order->deal_at = $payment->updated_at;
            $order->save();
        });
    }
}
