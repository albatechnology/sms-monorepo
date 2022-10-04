<?php

namespace Tests\Unit\API\Doc;

use App\Models\Address;

/**
 * Class AddressTest
 * @package Tests\Unit\API
 */
class AddressDocTest extends BaseApiDocTest
{
    protected Address $address;

    protected function setUp(): void
    {
        parent::setUp();
        $this->address = Address::factory()->create();
    }

    /**
     * @group Doc
     * @return void
     */
    public function testIndexAddress()
    {
        $this->makeApiTest(route('addresses.index', [], false), 'get');
    }

    /**
     * @group Doc
     * @return void
     */
    public function testCreateAddress()
    {
        $this->makeApiTest(route('addresses.create', [], false), 'get');
    }

    /**
     * @group Doc
     * @return void
     */
    public function testStoreAddress()
    {
        $data = Address::factory()->make()->toApiArray();
        $this->makeApiTest(route('addresses.store', [], false), 'post', $data, 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testShowAddress()
    {
        $this->makeApiTest(route('addresses.show', [$this->address->id], false), 'get', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testDestroyAddress()
    {
        $address = Address::factory()->create();
        $this->makeApiTest(route('addresses.destroy', [$address->id], false), 'delete', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testEditAddress()
    {
        $this->makeApiTest(route('addresses.edit', [$this->address->id], false), 'get', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testUpdateAddress()
    {
        $data = $this->address->toApiArray();
        $this->makeApiTest(route('addresses.update', [$this->address->id], false), 'put', $data, 0);
    }
}