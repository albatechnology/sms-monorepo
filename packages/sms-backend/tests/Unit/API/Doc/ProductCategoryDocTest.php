<?php

namespace Tests\Unit\API\Doc;

use App\Models\ProductCategory;

/**
 * Class ProductCategoryTest
 * @package Tests\Unit\API
 */
class ProductCategoryDocTest extends BaseApiDocTest
{
    protected ProductCategory $ProductCategory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ProductCategory = ProductCategory::factory()->create();
    }

    /**
     * @group Doc
     * @return void
     */
    public function testProductCategoryIndex()
    {
        $this->makeApiTest(route('product-categories.index', [], false), 'get', null, 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testProductCategoryShow()
    {
        $this->makeApiTest(route('product-categories.show', [$this->ProductCategory->id], false), 'get', [], 0);
    }
}