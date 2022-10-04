<?php

namespace Tests\Feature\Report\TargetType;

use App\Enums\OrderStatus;
use App\Enums\TargetType;
use App\Models\Channel;
use App\Models\Company;
use App\Models\Order;
use App\Models\Report;
use App\Models\User;
use App\Services\ReportService;
use Tests\Feature\BaseFeatureTest;

/**
 * Class ReportTest
 * @package Tests\Feature\Orders
 */
class DealsInvoicePriceTest extends BaseFeatureTest
{
    public $reportService;

    public function __construct()
    {
        parent::__construct();

        $this->reportService = app(ReportService::class);
    }

    public function testDealInvoiceReport(): void
    {
        $this->actingAs($this->sales);

        // first create report
        $report = Report::factory()
            ->forCompany($this->company)
            ->create();
        $this->assertDatabaseHas('reports', ['id' => $report->id]);

        // set target for the deals invoice
        $targetValue = 1000000;
        $this->reportService->setTarget($report, TargetType::DEALS_INVOICE_PRICE(), $targetValue);

        // initial value is 0
        $this->assertDatabaseHas('targets', [
            'type'   => TargetType::DEALS_INVOICE_PRICE,
            'target' => $targetValue,
            'value'  => 0
        ]);
        $this->assertDatabaseCount('target_maps', 0);

        // create orders to fill the target
        Order::factory()->asDeal()->create(['company_id' => $this->company, 'total_price' => $targetValue / 4]);
        Order::factory()->asDeal()->create(['company_id' => $this->company, 'total_price' => $targetValue / 4]);

        // assert target updated
        $this->assertDatabaseHas('targets', [
            'value'     => $targetValue / 2,
            'report_id' => $report->id,
            'type'      => TargetType::DEALS_INVOICE_PRICE,
        ]);
    }

    public function testCancelledOrderNotCounted()
    {
        $this->actingAs($this->sales);

        // first create report
        $report = Report::factory()
            ->forCompany($this->company)
            ->create();
        $this->assertDatabaseHas('reports', ['id' => $report->id]);

        // set target for the deals invoice
        $targetValue = 1000000;
        $this->reportService->setTarget($report, TargetType::DEALS_INVOICE_PRICE(), $targetValue);

        // initial value is 0
        $this->assertDatabaseHas('targets', [
            'type'   => TargetType::DEALS_INVOICE_PRICE,
            'target' => $targetValue,
            'value'  => 0
        ]);
        $this->assertDatabaseCount('target_maps', 0);

        // create orders to fill the target
        Order::factory()->asDeal()->create(['company_id' => $this->company, 'total_price' => $targetValue / 4]);

        // assert target updated
        $this->assertDatabaseHas('targets', [
            'value'     => $targetValue / 4,
            'report_id' => $report->id,
            'type'      => TargetType::DEALS_INVOICE_PRICE,
        ]);

        // second order
        $order = Order::factory()->asDeal()->create(['company_id' => $this->company, 'total_price' => $targetValue / 4]);

        // assert target updated
        $this->assertDatabaseHas('targets', [
            'value'     => $targetValue / 2,
            'report_id' => $report->id,
            'type'      => TargetType::DEALS_INVOICE_PRICE,
        ]);

        // cancel order
        $order->update(['status' => OrderStatus::CANCELLED]);

        // assert target updated
        $this->assertDatabaseHas('targets', [
            'value'     => $targetValue / 4,
            'report_id' => $report->id,
            'type'      => TargetType::DEALS_INVOICE_PRICE,
        ]);
    }

    public function testTargetForDifferentCompanyNotCounted(): void
    {
        $this->actingAs($this->sales);

        // first create report
        $report = Report::factory()
            ->forCompany($this->company)
            ->create();
        $this->assertDatabaseHas('reports', ['id' => $report->id]);

        // set target for the deals invoice
        $targetValue = 1000000;
        $this->reportService->setTarget($report, TargetType::DEALS_INVOICE_PRICE(), $targetValue);

        // initial value is 0
        $this->assertDatabaseHas('targets', [
            'type'   => TargetType::DEALS_INVOICE_PRICE,
            'target' => $targetValue,
            'value'  => 0
        ]);
        $this->assertDatabaseCount('target_maps', 0);

        // create orders to fill the target
        Order::factory()->asDeal()->create(['company_id' => $this->company, 'total_price' => $targetValue / 4]);

        // assert target updated
        $this->assertDatabaseHas('targets', [
            'value'     => $targetValue / 4,
            'report_id' => $report->id,
            'type'      => TargetType::DEALS_INVOICE_PRICE,
        ]);

        // create order again but against different company
        Order::factory()->asDeal()->create(['company_id' => Company::factory()->create(), 'total_price' => $targetValue / 4]);

        // assert target has not changed
        $this->assertDatabaseHas('targets', [
            'value'     => $targetValue / 4,
            'report_id' => $report->id,
            'type'      => TargetType::DEALS_INVOICE_PRICE,
        ]);
    }

    public function testTargetForDifferentChannelNotCounted(): void
    {
        $this->actingAs($this->sales);

        // first create report
        $report = Report::factory()
            ->forChannel($this->channel)
            ->create();
        $this->assertDatabaseHas('reports', ['id' => $report->id]);

        // set target for the deals invoice
        $targetValue = 1000000;
        $this->reportService->setTarget($report, TargetType::DEALS_INVOICE_PRICE(), $targetValue);

        // initial value is 0
        $this->assertDatabaseHas('targets', [
            'type'   => TargetType::DEALS_INVOICE_PRICE,
            'target' => $targetValue,
            'value'  => 0
        ]);
        $this->assertDatabaseCount('target_maps', 0);

        // create orders to fill the target
        Order::factory()->asDeal()->create(['channel_id' => $this->channel, 'total_price' => $targetValue / 4]);

        // assert target updated
        $this->assertDatabaseHas('targets', [
            'value'     => $targetValue / 4,
            'report_id' => $report->id,
            'type'      => TargetType::DEALS_INVOICE_PRICE,
        ]);

        // create order again but against different channel
        Order::factory()->asDeal()->create(['channel_id' => Channel::factory()->create(), 'total_price' => $targetValue / 4]);

        // assert target has not changed
        $this->assertDatabaseHas('targets', [
            'value'     => $targetValue / 4,
            'report_id' => $report->id,
            'type'      => TargetType::DEALS_INVOICE_PRICE,
        ]);
    }

    public function testTargetForDifferentUserNotCounted(): void
    {
        // create supervisor
        $supervisor = User::factory()->supervisor()->create();
        $this->actingAs($supervisor);

        // assign default user as supervised
        $this->sales->supervisor_id = $supervisor->id;
        $this->sales->save();

        // create another sales
        $sales2 = User::factory()->sales()->create();
        //dd(User::);

        // first create report
        $report = Report::factory()
            ->forUser($supervisor)
            ->create();
        $this->assertDatabaseHas('reports', ['id' => $report->id]);

        // set target for the deals invoice
        $targetValue = 1000000;
        $this->reportService->setTarget($report, TargetType::DEALS_INVOICE_PRICE(), $targetValue);

        // initial value is 0
        $this->assertDatabaseHas('targets', [
            'type'   => TargetType::DEALS_INVOICE_PRICE,
            'target' => $targetValue,
            'value'  => 0
        ]);
        $this->assertDatabaseCount('target_maps', 0);

        // create orders to fill the target
        Order::factory()->asDeal()->create(['user_id' => $supervisor->id, 'total_price' => $targetValue / 4]);

        // assert target updated
        $this->assertDatabaseHas('targets', [
            'value'     => $targetValue / 4,
            'report_id' => $report->id,
            'type'      => TargetType::DEALS_INVOICE_PRICE,
        ]);

        // create orders, under the supervised user
        Order::factory()->asDeal()->create(['user_id' => $this->sales->id, 'total_price' => $targetValue / 4]);

        // assert target updated
        $this->assertDatabaseHas('targets', [
            'value'     => $targetValue / 2,
            'report_id' => $report->id,
            'type'      => TargetType::DEALS_INVOICE_PRICE,
        ]);

        // create order again but under other non-supervised sales
        Order::factory()->asDeal()->create(['user_id' => $sales2->id, 'total_price' => $targetValue / 4]);

        // assert target has not changed
        $this->assertDatabaseHas('targets', [
            'value'     => $targetValue / 2,
            'report_id' => $report->id,
            'type'      => TargetType::DEALS_INVOICE_PRICE,
        ]);
    }
}
