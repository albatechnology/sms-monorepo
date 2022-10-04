<?php

namespace Tests\Unit\API\Doc;

use App\Models\Cart;

/**
 * Class CartTest
 * @package Tests\Unit\API
 */
class CartDocTest extends BaseApiDocTest
{
    protected Cart $Cart;

    protected function setUp(): void
    {
        parent::setUp();
        $this->Cart = Cart::factory()->create(['user_id' => $this->user->id]);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testCartIndex()
    {
        $this->makeApiTest(route('carts.index', [], false), 'get', [], 1);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testCartSync()
    {
        $data = Cart::factory()->make()->toApiArray();
        $this->makeApiTest(route('carts.sync', [], false), 'put', $data, 0);
    }
}