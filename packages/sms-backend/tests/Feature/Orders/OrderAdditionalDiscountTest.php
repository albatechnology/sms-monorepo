<?php

namespace Tests\Feature\Orders;

use App\Enums\OrderApprovalStatus;
use App\Enums\OrderPaymentStatus;
use App\Models\Payment;
use App\Models\ProductUnit;
use Tests\Feature\BaseFeatureTest;
use Tests\Helpers\TestCart;
use Tests\Helpers\TestCartLine;

class OrderAdditionalDiscountTest extends BaseFeatureTest
{

    /**
     * @param int $price
     * @return void
     * @throws \Exception
     */
    public function testOrderAdditionalDiscount(): void
    {
        $this->actingAs($this->sales);

        $price = 100000;
        $additionalDiscount = 10000;
        $productUnit = ProductUnit::factory()->create(['price' => $price]);

        $order = app(TestCart::class)
            ->addCartLine(TestCartLine::make($productUnit))
            ->createOrder();

        self::assertEquals(OrderApprovalStatus::NOT_REQUIRED, $order->approval_status->value);
        self::assertEquals($price, $order->total_price);

        $order2 = app(TestCart::class)
            ->addCartLine(TestCartLine::make($productUnit))
            ->createOrder(['additional_discount' => $additionalDiscount]);
        self::assertEquals(OrderApprovalStatus::WAITING_APPROVAL, $order2->approval_status->value);
        self::assertEquals($price - $additionalDiscount, $order2->total_price);

        $order3 = app(TestCart::class)
            ->addCartLine(TestCartLine::make($productUnit))
            ->createOrder(
                [
                    'additional_discount' => 10000,
                    'require_approval' => false
                ]
            );
        self::assertEquals(OrderApprovalStatus::NOT_REQUIRED, $order3->approval_status->value);

    }
}
