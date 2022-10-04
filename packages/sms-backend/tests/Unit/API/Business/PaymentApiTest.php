<?php

namespace Tests\Unit\API\Business;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Payment;

/**
 * Class CartTest
 * @package Tests\Unit\API
 */
class PaymentApiTest extends BaseApiTest
{

    /**
     * @return void
     */
    public function testCannotMakePaymentToCancelledOrder(): void
    {
        $this->actingAs($this->user);

        $order = Order::factory()->create(['status' => OrderStatus::CANCELLED]);
        $data  = Payment::factory()->make(['order_id' => $order->id])->toArray();

        $this->withHeaders(['Accept' => 'application/json'])
            ->post(route('payments.store', [], false), $data)
            // assert validation error on order_id
            ->assertStatus(422)
            ->assertJsonValidationErrors(['order_id']);
    }

}