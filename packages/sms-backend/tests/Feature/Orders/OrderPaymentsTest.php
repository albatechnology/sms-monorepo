<?php

namespace Tests\Feature\Orders;

use App\Enums\OrderPaymentStatus;
use App\Models\Payment;
use App\Models\ProductUnit;
use Tests\Feature\BaseFeatureTest;
use Tests\Helpers\TestCart;
use Tests\Helpers\TestCartLine;

class OrderPaymentsTest extends BaseFeatureTest
{

    /**
     * @dataProvider paymentsDataProvider
     * @param array $payments
     * @param int $price
     * @param string $status
     * @return void
     * @throws \Exception
     */
    public function testOrderPayment(array $payments, int $price, string $status): void
    {
        $this->actingAs($this->sales);

        // create product unit without stock
        $productUnit = ProductUnit::factory()->create(['price' => $price]);

        $order = app(TestCart::class)
            ->addCartLine(TestCartLine::make($productUnit))
            ->createOrder();

        self::assertEquals(OrderPaymentStatus::NONE()->description, $order->payment_status->description);

        foreach ($payments as $paymentAmount) {
            Payment::factory()->create(
                [
                    'company_id' => $order->company_id,
                    'order_id'   => $order->id,
                    'amount'     => $paymentAmount,
                ]
            );
        }
        $order->refreshPaymentStatus();

        self::assertEquals($status, $order->payment_status->description);
    }

    public function paymentsDataProvider()
    {
        return [
            'settlement'          => [
                'payments' => [100],
                'price'    => 100,
                'status'   => OrderPaymentStatus::SETTLEMENT()->description
            ],
            'settlement-multiple' => [
                'payments' => [25, 75],
                'price'    => 100,
                'status'   => OrderPaymentStatus::SETTLEMENT()->description
            ],
            'down_payment'        => [
                'payments' => [75],
                'price'    => 100,
                'status'   => OrderPaymentStatus::DOWN_PAYMENT()->description
            ],
            'none'                => [
                'payments' => [],
                'price'    => 100,
                'status'   => OrderPaymentStatus::NONE()->description
            ],
            'partial'             => [
                'payments' => [25],
                'price'    => 100,
                'status'   => OrderPaymentStatus::PARTIAL()->description
            ],
            'over_payment'        => [
                'payments' => [75, 50],
                'price'    => 100,
                'status'   => OrderPaymentStatus::OVERPAYMENT()->description
            ],
            'refunded'            => [
                'payments' => [50, -50],
                'price'    => 100,
                'status'   => OrderPaymentStatus::REFUNDED()->description
            ],
        ];
    }
}
