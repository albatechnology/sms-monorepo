<?php

namespace Tests\Unit\API\Doc;

use App\Models\Lead;
use App\Models\LeadCategory;

/**
 * Class LeadTest
 * @package Tests\Unit\API
 */
class LeadDocTest extends BaseApiDocTest
{
    protected Lead $lead;

    protected function setUp(): void
    {
        parent::setUp();
        $this->lead = Lead::factory()->create();
    }

    /**
     * @group Doc
     * @return void
     */
    public function testIndexLead()
    {
        $this->makeApiTest(route('leads.index', [], false), 'get');
    }

    /**
     * @group Doc
     * @return void
     */
    public function testIndexLeadUnhandled()
    {
        $this->makeApiTest(route('leads.unhandled', [], false), 'get');
    }

    /**
     * @group Doc
     * @return void
     */
    public function testIndexLeadCategory()
    {
        LeadCategory::factory()->create();
        $this->makeApiTest(route('leads.categories', [], false), 'get', [], 1);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testCreateLead()
    {
        $this->makeApiTest(route('leads.create', [], false), 'get', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testStoreLead()
    {
        $data = Lead::factory()->make()->toApiArray();
        //dd($data);
        $this->makeApiTest(route('leads.store', [], false), 'post', $data, 1);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testShowLead()
    {
        $this->makeApiTest(route('leads.show', [$this->lead->id], false), 'get', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testDestroyLead()
    {
        $lead = Lead::factory()->create();
        $this->makeApiTest(route('leads.destroy', [$lead->id], false), 'delete', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testEditLead()
    {
        $this->makeApiTest(route('leads.edit', [$this->lead->id], false), 'get', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testUpdateLead()
    {
        $data = $this->lead->toApiArray();
        $this->makeApiTest(route('leads.update', [$this->lead->id], false), 'put', $data, 0);
    }
}