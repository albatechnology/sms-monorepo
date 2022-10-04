<?php

namespace Tests\Unit\API\Doc;

use App\Models\Product;
use App\Models\ProductModel;
use Exception;

/**
 * Class ProductTest
 * @package Tests\Unit\API
 */
class ProductDocTest extends BaseApiDocTest
{
    protected Product $Product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->Product = Product::factory()->create();
    }

    /**
     * @group Doc
     * @return void
     * @throws Exception
     */
    public function testProductIndex()
    {
        $this->makeApiTest(route('products.index', [], false), 'get');
    }

    /**
     * @group Doc
     * @return void
     * @throws Exception
     */
    public function testProductShow()
    {
        $this->makeApiTest(route('products.show', [$this->Product->id], false), 'get', [], 0);
    }

    /**
     * @group Doc
     * @return void
     * @throws Exception
     */
    public function testProductBrands()
    {
        $this->makeApiTest(route('brands', [], false), 'get');
    }

    /**
     * @group Doc
     * @return void
     * @throws Exception
     */
    public function testProductModels()
    {
        $this->makeApiTest(route('models', [], false), 'get');
    }

    /**
     * @group Doc
     * @return void
     * @throws Exception
     */
    public function testProductModelById()
    {
        $model = ProductModel::where('company_id', $this->user->company_id)->first();
        $this->makeApiTest(route('models.get', [$model->id], false), 'get');
    }

    /**
     * @group Doc
     * @return void
     * @throws Exception
     */
    public function testProductVersions()
    {
        $this->makeApiTest(route('versions', [], false), 'get');
    }

    /**
     * @group Doc
     * @return void
     * @throws Exception
     */
    public function testProductCategoryCodes()
    {
        $this->makeApiTest(route('category-codes', [], false), 'get');
    }
}