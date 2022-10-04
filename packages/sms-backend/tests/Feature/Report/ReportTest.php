<?php

namespace Tests\Feature\Report;

use App\Enums\TargetType;
use App\Models\Channel;
use App\Models\Order;
use App\Models\Report;
use App\Models\Target;
use App\Models\TargetMap;
use App\Models\User;
use App\Services\ReportService;
use Tests\Feature\BaseFeatureTest;

/**
 *
 * Class ReportTest
 * @package Tests\Feature\Orders
 */
class ReportTest extends BaseFeatureTest
{
    public $reportService;

    public function __construct()
    {
        parent::__construct();

        $this->reportService = app(ReportService::class);
    }


    public function testReportCreatesDefaultTarget(): void
    {

        $this->actingAs($this->sales);

        // create report
        $report = Report::factory()->create();
        $this->assertDatabaseHas('reports', ['id' => $report->id]);

        // default target created
        foreach (TargetType::getDefaultInstances() as $enum) {
            $this->assertDatabaseHas('targets', ['type' => $enum->value]);
        }
    }

    public function testDealInvoiceReport()
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
        $order  = Order::factory()->asDeal()->create(['company_id' => $this->company, 'total_price' => $targetValue / 4]);
        $order2 = Order::factory()->asDeal()->create(['company_id' => $this->company, 'total_price' => $targetValue / 4]);

        // assert target mapping create
        $target = Target::query()
            ->where('report_id', $report->id)
            ->where('type', TargetType::DEALS_INVOICE_PRICE)
            ->first();

        // assert target updated
        $this->assertDatabaseHas('targets', [
            'id'    => $target->id,
            'value' => $targetValue / 2
        ]);

        // test the reevaluate report method.
        // force reset
        TargetMap::query()->delete();
        $target->update(['value' => 0]);
        $this->assertDatabaseHas('targets', [
            'id'    => $target->id,
            'value' => 0
        ]);

        // now reevaluate and assert
        app(ReportService::class)->reevaluateReport($report);
        $this->assertDatabaseHas('targets', [
            'id'    => $target->id,
            'value' => $targetValue / 2
        ]);

        //reverse a deal from order
        $order->refresh()->update(['deal_at' => null]);

        $this->assertDatabaseHas('targets', [
            'id'    => $target->id,
            'value' => $targetValue / 4
        ]);

    }

    public function testCreateReportAfterOrder()
    {
        $this->actingAs($this->sales);

        $targetValue = 1000000;

        // create orders to fill the target
        $order1 = Order::factory()->asDeal()->create(['company_id' => $this->company, 'total_price' => $targetValue / 4]);
        $order2 = Order::factory()->asDeal()->create(['company_id' => $this->company, 'total_price' => $targetValue / 4]);

        // now create report
        $report = Report::factory()
            ->forCompany($this->company)
            ->create();
        $this->reportService->setTarget($report, TargetType::DEALS_INVOICE_PRICE(), $targetValue);

        $this->assertDatabaseHas('target_maps', ['model_type' => Order::class, 'model_id' => $order1->id]);
        $this->assertDatabaseHas('target_maps', ['model_type' => Order::class, 'model_id' => $order2->id]);

        // initial value is 0
        $this->assertDatabaseHas('targets', [
            'type'   => TargetType::DEALS_INVOICE_PRICE,
            'target' => $targetValue,
            'value'  => $targetValue / 2
        ]);
    }

    public function testSupervisorCanAccessChannelTarget()
    {
        $user = User::factory()->supervisor()->withChannel()->create();
        $this->actingAs($user);

        $report = Report::factory()
            ->forChannel(Channel::find($user->channel_id))
            ->create();

        $this->assertNotEquals(0, Target::tenanted()->count());
    }
}
