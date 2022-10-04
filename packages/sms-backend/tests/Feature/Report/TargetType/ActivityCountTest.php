<?php

namespace Tests\Feature\Report\TargetType;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\TargetType;
use App\Models\Activity;
use App\Models\Order;
use App\Models\Report;
use App\Services\ReportService;
use Tests\Feature\BaseFeatureTest;

/**
 *
 * Class ActivityCountTest
 * @package Tests\Feature\Orders
 */
class ActivityCountTest extends BaseFeatureTest
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

        $channel = $this->company->companyChannels->first();

        // set target for the activity
        $targetValue = 2;
        $this->reportService->setTarget($report, TargetType::ACTIVITY_COUNT(), $targetValue);

        // initial value is 0
        $this->assertDatabaseHas('targets', [
            'type'   => TargetType::ACTIVITY_COUNT,
            'target' => $targetValue,
            'value'  => 0
        ]);
        $this->assertDatabaseCount('target_maps', 0);

        // create orders to fill the target
        $model1 = Activity::factory()->create(['channel_id' => $channel]);

        // assert target updated
        $this->assertDatabaseHas('targets', [
            'type'      => TargetType::ACTIVITY_COUNT,
            'report_id' => $report->id,
            'value'     => 1,
        ]);

        // create activity to fill the target
        $model2 = Activity::factory()->create(['channel_id' => $channel]);

        // assert target updated
        $this->assertDatabaseHas('targets', [
            'type'      => TargetType::ACTIVITY_COUNT,
            'report_id' => $report->id,
            'value'     => 2,
        ]);
    }
}
