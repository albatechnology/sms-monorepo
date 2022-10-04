<?php

namespace Tests\Unit\API\Doc;

use App\Models\Payment;

/**
 * Class PaymentTest
 * @package Tests\Unit\API
 */
class PaymentApiDocTest extends BaseApiDocTest
{
    protected Payment $Payment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->user);
        $this->Payment = Payment::factory()->create();
    }

    /**
     * @group Doc
     * @return void
     */
    public function testPaymentIndex()
    {
        $this->makeApiTest(route('payments.index', [], false), 'get', [], 1);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testPaymentCreate()
    {
        $this->makeApiTest(route('payments.create', [], false), 'get');
    }

    /**
     * @group Doc
     * @return void
     */
    public function testPaymentStore()
    {
        $data = Payment::factory()->make()->toArray();
        $this->makeApiTest(route('payments.store', [], false), 'post', $data);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testPaymentShow()
    {
        $this->makeApiTest(route('payments.show', [$this->Payment->id], false), 'get', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testPaymentEdit()
    {
        $this->makeApiTest(route('payments.edit', [$this->Payment->id], false), 'get', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testPaymentUpdate()
    {
        $data = $this->Payment->toArray();
        $this->makeApiTest(route('payments.update', [$this->Payment->id], false), 'put', $data, 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testPaymentCategoryIndex()
    {
        $this->makeApiTest(route('payment-categories.index', [], false), 'get', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testPaymentTypeIndex()
    {
        $this->makeApiTest(route('payment-types.index', [], false), 'get', [], 0);
    }
}