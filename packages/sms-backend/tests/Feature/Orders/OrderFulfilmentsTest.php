<?php

namespace Tests\Feature\Orders;

use App\Enums\OrderDetailStatus;
use App\Enums\OrderStockStatus;
use App\Models\ProductUnit;
use App\Models\Stock;
use Tests\Feature\BaseFeatureTest;
use Tests\Helpers\TestCart;
use Tests\Helpers\TestCartLine;
use Tests\Helpers\TestService;

/**
 * Test that order can be fulfilled and stock are deducted.
 *
 * Class OrderFulfilmentsTest
 * @package Tests\Feature\Orders
 */
class OrderFulfilmentsTest extends BaseFeatureTest
{

    /**
     * @dataProvider fulfilmentDataProvider
     * @param int $quantity
     * @param int $stock
     * @param string $stock_status
     * @param string $detail_status
     * @param int $indent
     * @param int $end_stock
     * @return void
     * @throws \App\Exceptions\InsufficientStockException
     */
    public function testOrderDetailFulfilment(
        int $quantity,
        int $stock,
        string $stock_status,
        string $detail_status,
        int $indent,
        int $end_stock
    ): void
    {
        $this->actingAs($this->sales);

        // setup product unit, stock and order
        /** @var ProductUnit $productUnit */
        $productUnit = ProductUnit::factory()->withStock($stock)->create();
        $order       = app(TestCart::class)
            ->addCartLine(TestCartLine::make($productUnit, $quantity))
            ->createOrder();

        // Pay the order, and the app will try to fulfil the order
        TestService::payOrder($order);
        $stockModel = Stock::query()
            ->where('product_unit_id', $productUnit->id)
            ->where('channel_id', $order->channel_id)
            ->first();

        // assert result
        self::assertEquals($stock_status, $order->stock_status->description);
        self::assertEquals($indent, $order->order_details->first()->indent);
        self::assertEquals($detail_status, $order->order_details->first()->status->description);
        self::assertEquals($end_stock, $stockModel->stock);
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
