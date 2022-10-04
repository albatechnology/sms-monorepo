<?php

namespace Tests\Feature\Report\TargetType;

use App\Enums\OrderStatus;
use App\Enums\TargetType;
use App\Models\Channel;
use App\Models\Company;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Report;
use App\Models\Target;
use App\Models\User;
use App\Services\ReportService;
use Tests\Feature\BaseFeatureTest;

/**
 * Class ReportTest
 * @package Tests\Feature\Orders
 */
class DealsBrandPriceTest extends BaseFeatureTest
{
    public $reportService;

    public function __construct()
    {
        parent::__construct();

        $this->reportService = app(ReportService::class);
    }

    public function testDealBrandPrice(): void
    {
        $this->actingAs($this->sales);

        // first create report
        $report = Report::factory()
            ->forCompany($this->company)
            ->create();
        $this->assertDatabaseHas('reports', ['id' => $report->id]);

        // set target for the deals brand
        $targetValue = 1000000;
        $this->reportService->setTarget($report, TargetType::DEALS_BRAND_PRICE(), $targetValue);

        // initial value is 0
        $this->assertDatabaseHas('targets', [
            'type'   => TargetType::DEALS_BRAND_PRICE,
            'target' => $targetValue,
            'value'  => 0
        ]);
        $this->assertDatabaseCount('target_maps', 0);
        $target = Target::where('type', TargetType::DEALS_BRAND_PRICE)->first();

        // create orders to fill the target
        $product     = Product::factory()->withProductUnits()->create();
        $productUnit = $product->productUnits->first();
        $productUnit->update(['price' => $targetValue / 4]);
        $brand       = $product->brand;

        $details = OrderDetail::factory()
            ->forProductUnit($productUnit)
            ->count(3)
            ->create();

        $order1 = Order::factory()
            ->withOrderDetails(
                $details[0]
            )
            ->asDeal()
            ->create();

        // assert target updated
        $this->assertDatabaseHas('target_lines', [
            'target_id'  => $target->id,
            'model_type' => get_class($brand),
            'model_id'   => $brand->id,
            'value'      => $targetValue / 4,
        ]);

        $order2 = Order::factory()
            ->withOrderDetails(
                $details[1]
            )
            ->asDeal()
            ->create();

        // assert target updated
        $this->assertDatabaseHas('target_lines', [
            'target_id'  => $target->id,
            'model_type' => get_class($brand),
            'model_id'   => $brand->id,
            'value'      => $targetValue / 2,
        ]);

        $order3 = Order::factory()
            ->withOrderDetails(
                $details[2]
            )
            ->asDeal()
            ->create();

        // assert target updated
        $this->assertDatabaseHas('target_lines', [
            'target_id'  => $target->id,
            'model_type' => get_class($brand),
            'model_id'   => $brand->id,
            'value'      => $targetValue * 3 / 4,
        ]);

        // test cancel the order
        $order1->refresh()->update(['status' => OrderStatus::CANCELLED]);

        // assert target updated
        $this->assertDatabaseHas('target_lines', [
            'target_id'  => $target->id,
            'model_type' => get_class($brand),
            'model_id'   => $brand->id,
            'value'      => $targetValue / 2,
        ]);
    }

}
