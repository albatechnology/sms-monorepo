<?php

namespace Tests\Unit\API\Doc;

use App\Models\ProductUnit;

/**
 * Class ProductUnitTest
 * @package Tests\Unit\API
 */
class ProductUnitDocTest extends BaseApiDocTest
{
    protected ProductUnit $ProductUnit;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ProductUnit = ProductUnit::factory()->create();
    }

    /**
     * @group Doc
     * @return void
     */
    public function testProductUnitIndex()
    {
        $this->makeApiTest(route('product-units.index', [], false), 'get');
    }

    /**
     * @group Doc
     * @return void
     */
    public function testProductUnitShow()
    {
        $this->makeApiTest(route('product-units.show', [$this->ProductUnit->id], false), 'get', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testProductUnitColours()
    {
        $this->makeApiTest(route('colours', [], false), 'get', null, 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testProductUnitCoverings()
    {
        $this->makeApiTest(route('coverings', [], false), 'get');
    }
}