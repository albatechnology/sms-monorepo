<?php

namespace Tests\Feature\Report\TargetType;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\TargetType;
use App\Models\Order;
use App\Models\Report;
use App\Services\ReportService;
use Tests\Feature\BaseFeatureTest;

/**
 *
 * Class DealsOrderCountTest
 * @package Tests\Feature\Orders
 */
class DealsOrderCountTest extends BaseFeatureTest
{
    public $reportService;

    public function __construct()
    {
        parent::__construct();

        $this->reportService = app(ReportService::class);
    }

    public function testDealsOrderCount(): void
    {
        $this->actingAs($this->sales);

        // first create report
        $report = Report::factory()
            ->forCompany($this->company)
            ->create();
        $this->assertDatabaseHas('reports', ['id' => $report->id]);

        // set target for the deals invoice
        $targetValue = 2;
        $this->reportService->setTarget($report, TargetType::DEALS_ORDER_COUNT(), $targetValue);

        // initial value is 0
        $this->assertDatabaseHas('targets', [
            'type'   => TargetType::DEALS_ORDER_COUNT,
            'target' => $targetValue,
            'value'  => 0
        ]);
        $this->assertDatabaseCount('target_maps', 0);

        // create orders to fill the target
        $order1 = Order::factory()->asDeal()->create(['company_id' => $this->company, 'total_price' => 100]);

        // assert target updated
        $this->assertDatabaseHas('targets', [
            'type'      => TargetType::DEALS_ORDER_COUNT,
            'report_id' => $report->id,
            'value'     => 1,
        ]);

        // create orders to fill the target
        $order2 = Order::factory()->asDeal()->create(['company_id' => $this->company, 'total_price' => 100]);

        // assert target updated
        $this->assertDatabaseHas('targets', [
            'type'      => TargetType::DEALS_ORDER_COUNT,
            'report_id' => $report->id,
            'value'     => 2,
        ]);

        // test cancel the order
        $order1->update(['status' => OrderStatus::CANCELLED]);

        // assert target updated
        $this->assertDatabaseHas('targets', [
            'type'      => TargetType::DEALS_ORDER_COUNT,
            'report_id' => $report->id,
            'value'     => 1,
        ]);

        // test reverse deal the order
        $order2->orderPayments()->first()->update(['status' => PaymentStatus::REJECTED]);

        // assert target updated
        $this->assertDatabaseHas('targets', [
            'type'      => TargetType::DEALS_ORDER_COUNT,
            'report_id' => $report->id,
            'value'     => 0,
        ]);
    }
}
