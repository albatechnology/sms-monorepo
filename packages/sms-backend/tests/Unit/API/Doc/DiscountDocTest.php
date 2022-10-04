<?php

namespace Tests\Unit\API\Doc;

use App\Models\Discount;

/**
 * Class CartTest
 * @package Tests\Unit\API
 */
class DiscountDocTest extends BaseApiDocTest
{
    protected Discount $discount;

    protected function setUp(): void
    {
        parent::setUp();
        $this->discount = Discount::factory()->create();
    }

    /**
     * @group Doc
     * @return void
     */
    public function testDiscountIndex()
    {
        $this->makeApiTest(route('discounts.index', [], false), 'get', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testDiscountGetByCode()
    {
        Discount::factory()->create(['activation_code' => 'TESTCODE']);
        $this->makeApiTest(route('discounts.code', ['TESTCODE'], false), 'get', [], 1);
        // test 404 response
        $this->makeApiTest(route('discounts.code', ['ABC'], false), 'get', [], 1);
    }
}