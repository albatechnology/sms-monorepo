<?php

namespace Tests\Unit\API\Doc;

use App\Models\ProductUnit;
use App\Models\Stock;

/**
 * Class StockTest
 * @package Tests\Unit\API
 */
class StockDocTest extends BaseApiDocTest
{
    protected Stock $Stock;

    protected function setUp(): void
    {
        parent::setUp();
        $productUnit = ProductUnit::factory()->create();
        $this->Stock = $productUnit->stocks->first();
    }

    /**
     * @group Doc
     * @return void
     */
    public function testStockIndex()
    {
        $this->makeApiTest(route('stocks.index', [], false), 'get', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testStockIndexExtended()
    {
        $this->makeApiTest(route('stocks.index.extended', [], false), 'get', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testStockShow()
    {
        $this->makeApiTest(route('stocks.show', [$this->Stock->id], false), 'get', [], 0);
    }
}