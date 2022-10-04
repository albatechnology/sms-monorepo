<?php

namespace Tests\Unit\Models\Lead;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Enums\OrderPaymentStatus;
use App\Enums\PaymentStatus;
use App\Models\Lead;
use App\Models\Order;
use App\Models\PaymentType;
use App\Services\OrderService;
use Tests\Unit\Models\BaseModelTest;


class LeadOrderTest extends BaseModelTest
{
    protected Order $order;
    protected Lead $lead;

    /**
     * @return void
     */
    public function testLeadIsClosedAfterOrderSettlement()
    {
        $this->actingAs($this->user);

        $this->assertEquals(LeadStatus::GREEN, $this->lead->status->value);
        $this->assertEquals(LeadType::PROSPECT, $this->lead->type->value);
        self::assertEquals(OrderPaymentStatus::NONE()->description, $this->order->payment_status->description);


        // Pay the order
        app(OrderService::class)->makeOrderPayment(
            $this->order->total_price,
            PaymentType::factory()->forOrder($this->order)->create()->id,
            $this->order->id,
            status: PaymentStatus::APPROVED()
        );

        $this->lead->refresh();
        $this->order->refresh();

        self::assertEquals(OrderPaymentStatus::SETTLEMENT()->description, $this->order->payment_status->description);
        self::assertEquals(LeadStatus::SALES()->description, $this->lead->status->description);
        self::assertEquals(LeadType::DEAL()->description, $this->lead->type->description);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->order = Order::factory()->create();
        $this->lead  = $this->order->lead;
    }
}
