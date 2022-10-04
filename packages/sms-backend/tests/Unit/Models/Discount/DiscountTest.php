<?php

namespace Tests\Unit\Models\Discount;

use App\Models\Cart;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\ProductUnit;
use App\Services\OrderService;
use Tests\Unit\Models\BaseModelTest;

class DiscountTest extends BaseModelTest
{
    protected Customer $customer;
    protected Cart     $cart;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = Customer::factory()->create();
        $this->cart     = Cart::create(
            [
                'customer_id' => $this->customer->id,
                'user_id'     => $this->user->id
            ]
        );

    }

    /**
     * @return void
     */
    public function testOrderPercentageDiscount()
    {
        $discount = Discount::factory()->value(30)->create();

        $this->cart->addProductUnit(ProductUnit::factory()->create(['price' => 100000]));
        $this->cart->addProductUnit(ProductUnit::factory()->create(['price' => 100000]));

        OrderService::setDiscount($this->cart, $discount);

        $this->assertSame(140000, $this->cart->total_price);
        $this->assertSame(60000, $this->cart->total_discount);
    }

    /**
     * @return void
     */
    public function testOrderNominalDiscount()
    {
        $discount = Discount::factory()->nominal()->value(50000)->create();

        $this->cart->addProductUnit(ProductUnit::factory()->create(['price' => 100000]));
        $this->cart->addProductUnit(ProductUnit::factory()->create(['price' => 100000]));

        OrderService::setDiscount($this->cart, $discount);

        $this->assertSame(150000, $this->cart->total_price);
        $this->assertSame(50000, $this->cart->total_discount);
    }

    /**
     * @return void
     */
    public function testProductPercentDiscount()
    {
        $discount = Discount::factory()->scopeType()->value(10)->create();

        $item1 = ProductUnit::factory()->create(['price' => 10]);
        $item2 = ProductUnit::factory()->create(['price' => 100]);

        $this->cart->addProductUnit($item1);
        $this->cart->addProductUnit($item2);
        OrderService::setDiscount($this->cart, $discount);

        $this->assertSame(99, $this->cart->total_price);
        $this->assertSame(11, $this->cart->total_discount);
        $this->assertSame(9, $this->cart->getItem($item1->id)->total_price);
        $this->assertSame(90, $this->cart->getItem($item2->id)->total_price);
    }
}
