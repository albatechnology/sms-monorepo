<?php

namespace Tests\Unit\API\Doc;

use App\Models\ProductTag;

/**
 * Class ProductTagTest
 * @package Tests\Unit\API
 */
class ProductTagDocTest extends BaseApiDocTest
{
    protected ProductTag $ProductTag;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ProductTag = ProductTag::factory()->create([
            "company_id" => $this->user->company_id
        ]);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testProductTagIndex()
    {
        $this->makeApiTest(route('product-tags.index', [], false), 'get');
    }

    /**
     * @group Doc
     * @return void
     */
    public function testProductTagShow()
    {
        $this->makeApiTest(route('product-tags.show', [$this->ProductTag->id], false), 'get', [], 0);
    }

}