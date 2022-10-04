<?php

namespace Tests\Unit\API\Doc;

use App\Models\Activity;
use App\Models\Address;
use App\Models\Customer;
use App\Models\Lead;

/**
 * Class CustomerTest
 * @package Tests\Unit\API
 */
class CustomerDocTest extends BaseApiDocTest
{
    protected Customer $customer;

    /**
     * @group Doc
     * @return void
     */
    public function testIndexCustomer()
    {
        $this->makeApiTest(route('customers.index', [], false), 'get');
    }

    /**
     * @group Doc
     * @return void
     */
    public function testCreateCustomer()
    {
        $this->makeApiTest(route('customers.create', [], false), 'get');
    }

    /**
     * @group Doc
     * @return void
     */
    public function testStoreCustomer()
    {
        $data = Customer::factory()->make()->toApiArray();
        $this->makeApiTest(route('customers.store', [], false), 'post', $data, 1);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testShowCustomer()
    {
        $this->makeApiTest(route('customers.show', [$this->customer->id], false), 'get', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testDestroyCustomer()
    {
        $customer = Customer::factory()->create();
        $this->makeApiTest(route('customers.destroy', [$customer->id], false), 'delete', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testEditCustomer()
    {
        $this->makeApiTest(route('customers.edit', [$this->customer->id], false), 'get', [], 1);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testUpdateCustomer()
    {
        $data = $this->customer->toApiArray();
        $this->makeApiTest(route('customers.update', [$this->customer->id], false), 'put', $data, 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testCustomerAddressCreate()
    {
        $this->makeApiTest(route('customers.addresses.create', [], false), 'get', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testCustomerAddressStore()
    {
        $customer = Customer::factory()->make();
        $address  = Address::factory()->make();
        $data     = array_merge($customer->toApiArray(), $address->toApiArray());

        $this->makeApiTest(route('customers.addresses.store', [], false), 'post', $data, 1);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testCustomerGetLeads()
    {
        Lead::factory()->create(["customer_id" => $this->customer->id]);
        $this->makeApiTest(route('customers.leads', [$this->customer->id], false), 'get');
    }

    /**
     * @group Doc
     * @return void
     */
    public function testCustomerGetActivities()
    {
        Activity::factory()->create(["customer_id" => $this->customer->id]);
        $this->makeApiTest(route('customers.activities', [$this->customer->id], false), 'get', null, 0);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = Customer::factory()->create();
    }
}
