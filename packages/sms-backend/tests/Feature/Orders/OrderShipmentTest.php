<?php

namespace Tests\Feature\Orders;

use App\Enums\OrderDetailStatus;
use App\Enums\OrderShipmentStatus;
use App\Enums\OrderStockStatus;
use App\Enums\ShipmentStatus;
use App\Models\ProductUnit;
use Tests\Feature\BaseFeatureTest;
use Tests\Helpers\TestCart;
use Tests\Helpers\TestCartLine;
use Tests\TestHelper;

/**
 * Test that order can be fulfilled and stock are deducted.
 *
 * Class OrderFulfilmentsTest
 * @package Tests\Feature\Orders
 */
class OrderShipmentTest extends BaseFeatureTest
{

    public function testOrderShipment()
    {

        $this->actingAs($this->sales);

        // setup product unit, stock and order
        $productUnit1 = ProductUnit::factory()->withStock()->create();
        $productUnit2 = ProductUnit::factory()->withStock()->create();

        $order = app(TestCart::class)
            ->addCartLine(TestCartLine::make($productUnit1, 2))
            ->addCartLine(TestCartLine::make($productUnit2, 2))
            ->createOrder();

        self::assertEquals(OrderShipmentStatus::NONE()->description, $order->shipment_status->description);

        $shipment = app(TestCart::class)
            ->addCartLine(TestCartLine::make($productUnit1, 2))
            ->addCartLine(TestCartLine::make($productUnit2, 2))
            ->createShipment($order, ShipmentStatus::PREPARING());
        self::assertEquals(OrderShipmentStatus::PREPARING()->description, $order->refresh()->shipment_status->description);

        $shipment->update(['status' => ShipmentStatus::DELIVERING()]);
        self::assertEquals(OrderShipmentStatus::DELIVERING()->description, $order->refresh()->shipment_status->description);

        $shipment->update(['status' => ShipmentStatus::ARRIVED()]);
        self::assertEquals(OrderShipmentStatus::ARRIVED()->description, $order->refresh()->shipment_status->description);

        $shipment->update(['status' => ShipmentStatus::CANCELLED()]);
        self::assertEquals(OrderShipmentStatus::NONE()->description, $order->refresh()->shipment_status->description);

        // now lets test partial shipment
        $shipment2 = app(TestCart::class)
            ->addCartLine(TestCartLine::make($productUnit1, 1))
            ->createShipment($order, ShipmentStatus::ARRIVED());
        self::assertEquals(OrderShipmentStatus::PARTIAL()->description, $order->refresh()->shipment_status->description);

        // another partial with all the remaining item, but not arrived yet
        $shipment3 = app(TestCart::class)
            ->addCartLine(TestCartLine::make($productUnit1, 1))
            ->addCartLine(TestCartLine::make($productUnit2, 2))
            ->createShipment($order, ShipmentStatus::DELIVERING());
        self::assertEquals(OrderShipmentStatus::PARTIAL()->description, $order->refresh()->shipment_status->description);

        $shipment3->update(['status' => ShipmentStatus::ARRIVED()]);
        self::assertEquals(OrderShipmentStatus::ARRIVED()->description, $order->refresh()->shipment_status->description);

    }

    public function fulfilmentDataProvider()
    {
        return [
            'fulfilled'         => [
                'quantity'      => 2,
                'stock'         => 10,
                'stock_status'  => OrderStockStatus::FULFILLED()->description,
                'detail_status' => OrderDetailStatus::FULFILLED()->description,
                'indent'        => 0,
                'end_stock'     => 8,
            ],
            'not_fulfilled'     => [
                'quantity'      => 2,
                'stock'         => 0,
                'stock_status'  => OrderStockStatus::INDENT()->description,
                'detail_status' => OrderDetailStatus::NOT_FULFILLED()->description,
                'indent'        => 2,
                'end_stock'     => 0,
            ],
            'partial_fulfilled' => [
                'quantity'      => 2,
                'stock'         => 1,
                'stock_status'  => OrderStockStatus::INDENT()->description,
                'detail_status' => OrderDetailStatus::PARTIALLY_FULFILLED()->description,
                'indent'        => 1,
                'end_stock'     => 0,
            ],
        ];
    }
}
