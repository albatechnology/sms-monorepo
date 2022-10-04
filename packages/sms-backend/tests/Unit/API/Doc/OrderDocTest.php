<?php

namespace Tests\Unit\API\Doc;

use App\Models\Address;
use App\Models\Lead;
use App\Models\Order;
use App\Models\ProductUnit;

/**
 * Class OrderDocTest
 * @package Tests\Unit\API
 */
class OrderDocTest extends BaseApiDocTest
{
    protected Order $order;

    /**
     * @group Doc
     * @return void
     */
    public function testStoreOrder()
    {
        $address = Address::factory()->create();
        $data    = [
            "items"               => [
                [
                    "id"       => ProductUnit::factory()->create()->id,
                    "quantity" => 1
                ]
            ],
            // "discount_id"          => ,
            //"expected_total_price" => 1000,
            "shipping_address_id"        => $address->id,
            "billing_address_id"         => $address->id,
            "lead_id"                    => Lead::factory()->create()->id,
            //"tax_invoice_id"       => 1,
            "note"                       => "Note placed on order",
            "expected_shipping_datetime" => now()->addDay()->toString()
        ];

        $this->makeApiTest(route('orders.store', [], false), 'post', $data, 1);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testPreviewOrder()
    {
        $address = Address::factory()->create();
        $data    = [
            "items"               => [
                [
                    "id"       => ProductUnit::factory()->create()->id,
                    "quantity" => 1
                ]
            ],
            "shipping_address_id"        => $address->id,
            "billing_address_id"         => $address->id,
            "lead_id"                    => Lead::factory()->create()->id,
            "note"                       => "Note placed on order",
            "expected_shipping_datetime" => now()->addDay()->toString()
        ];

        $this->makeApiTest(route('orders.preview', [], false), 'post', $data, 1);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testShowOrder()
    {
        $this->makeApiTest(route('orders.show', [$this->order->id], false), 'get', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testIndexLead()
    {
        $this->makeApiTest(route('orders.index', [], false), 'get');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->order = Order::factory()->create();
    }
}