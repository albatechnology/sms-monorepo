<?php

namespace Tests\Feature\Report\TargetType;

use App\Enums\PaymentStatus;
use App\Enums\TargetType;
use App\Models\Order;
use App\Models\Report;
use App\Services\ReportService;
use Tests\Feature\BaseFeatureTest;

/**
 *
 * Class ReportTest
 * @package Tests\Feature\Orders
 */
class DealsPaymentPriceTest extends BaseFeatureTest
{
    public $reportService;

    public function __construct()
    {
        parent::__construct();

        $this->reportService = app(ReportService::class);
    }

    public function testDealPaymentPrice(): void
    {
        $this->actingAs($this->sales);

        // first create report
        $report = Report::factory()
            ->forCompany($this->company)
            ->create();
        $this->assertDatabaseHas('reports', ['id' => $report->id]);

        // set target
        $targetValue = 1000000;
        $this->reportService->setTarget($report, TargetType::DEALS_PAYMENT_PRICE(), $targetValue);

        // initial value is 0
        $this->assertDatabaseHas('targets', [
            'type'   => TargetType::DEALS_PAYMENT_PRICE,
            'target' => $targetValue,
            'value'  => 0
        ]);
        $this->assertDatabaseCount('target_maps', 0);

        // create orders to fill the target
        Order::factory()->asDeal()->create(['company_id' => $this->company, 'total_price' => $targetValue / 4]);
        Order::factory()->asDeal()->create(['company_id' => $this->company, 'total_price' => $targetValue / 4]);

        // assert target updated
        $this->assertDatabaseHas('targets', [
            'type'      => TargetType::DEALS_PAYMENT_PRICE,
            'report_id' => $report->id,
            'value'     => $targetValue / 2,
        ]);
    }


    public function testCancelledPaymentNotCounted()
    {
        $this->actingAs($this->sales);

        // first create report
        $report = Report::factory()
            ->forCompany($this->company)
            ->create();
        $this->assertDatabaseHas('reports', ['id' => $report->id]);

        // set target for the deals invoice
        $targetValue = 1000000;
        $this->reportService->setTarget($report, TargetType::DEALS_PAYMENT_PRICE(), $targetValue);

        // initial value is 0
        $this->assertDatabaseHas('targets', [
            'type'   => TargetType::DEALS_PAYMENT_PRICE,
            'target' => $targetValue,
            'value'  => 0
        ]);
        $this->assertDatabaseCount('target_maps', 0);

        // create paid order to fill the target
        Order::factory()->asDeal()->create(['company_id' => $this->company, 'total_price' => $targetValue / 4]);

        // assert target updated
        $this->assertDatabaseHas('targets', [
            'value'     => $targetValue / 4,
            'report_id' => $report->id,
            'type'      => TargetType::DEALS_PAYMENT_PRICE,
        ]);

        // second payment
        /** @var Order $order */
        $order = Order::factory()->asDeal()->create(['company_id' => $this->company, 'total_price' => $targetValue / 4]);

        // assert target updated
        $this->assertDatabaseHas('targets', [
            'type'      => TargetType::DEALS_PAYMENT_PRICE,
            'report_id' => $report->id,
            'value'     => $targetValue / 2,
        ]);

        $this->assertDatabaseHas('targets', [
            'type'      => TargetType::DEALS_INVOICE_PRICE,
            'report_id' => $report->id,
            'value'     => $targetValue / 2,
        ]);

        // additional check to see that when a cancelled payment could remove the
        // detail status of an order
        $this->assertDatabaseMissing('orders', [
            'id'      => $order->id,
            'deal_at' => null
        ]);

        // disapprove payment order
        $order->orderPayments()->first()->update(['status' => PaymentStatus::REJECTED]);

        // assert target updated
        $this->assertDatabaseHas('targets', [
            'type'      => TargetType::DEALS_PAYMENT_PRICE,
            'report_id' => $report->id,
            'value'     => $targetValue / 4,
        ]);

        $this->assertDatabaseHas('targets', [
            'type'      => TargetType::DEALS_INVOICE_PRICE,
            'report_id' => $report->id,
            'value'     => $targetValue / 4,
        ]);

        $this->assertDatabaseHas('orders', [
            'id'      => $order->id,
            'deal_at' => null
        ]);


    }
}
