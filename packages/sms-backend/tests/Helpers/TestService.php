<?php


namespace Tests\Helpers;


use App\Models\Order;
use App\Models\Payment;

class TestService
{
    public static function payOrder(Order $order): Order
    {
        Payment::factory()->forOrderSettlement($order)->create();
        $order->refreshPaymentStatus();
        return $order;
    }
}