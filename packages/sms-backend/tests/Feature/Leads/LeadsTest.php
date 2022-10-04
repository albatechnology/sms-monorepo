<?php

namespace Tests\Feature\Leads;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Models\Activity;
use App\Models\Channel;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
use Tests\Feature\BaseFeatureTest;

class LeadsTest extends BaseFeatureTest
{
    /**
     * Customer visiting multiple channel should be registered as
     * the same leads group id. When a leads on a group is closed, it should
     * also closes all other leads in the same group id.
     *
     * @return void
     */
    public function testLeadsGroupId()
    {
//        $this->actingAs($this->sales);

        // first lead
        $lead1 = Lead::create(
            [
                'type'        => LeadType::LEADS,
                'label'       => 'lead1',
                'customer_id' => $this->customer->id,
                'channel_id'  => $this->channel->id,
                'user_id'     => $this->sales->id,
                'status'      => LeadStatus::GREEN()
            ]
        )->refresh();

        // same customer visiting other channel
        $channel2 = Channel::factory()->create();
        $sales2   = User::factory()->sales()->create();
        $lead2    = Lead::create(
            [
                'type'        => LeadType::LEADS,
                'label'       => 'test lead 1',
                'customer_id' => $this->customer->id,
                'channel_id'  => $channel2->id,
                'user_id'     => $sales2->id,
                'status'      => LeadStatus::GREEN()
            ]
        )->refresh();

        // different customer
        $customer2 = Customer::factory()->create();
        $lead3     = Lead::create(
            [
                'type'        => LeadType::LEADS,
                'label'       => 'lead3',
                'customer_id' => $customer2->id,
                'channel_id'  => $this->channel->id,
                'user_id'     => $this->sales->id,
                'status'      => LeadStatus::GREEN()
            ]
        )->refresh();

        // test group id assignment
        self::assertEquals($lead1->group_id, $lead2->group_id);
        self::assertNotEquals($lead1->group_id, $lead3->group_id);

        // test cascading closed group id
        $lead1->closeAsSales();
        $lead2->refresh();
        $lead3->refresh();
        self::assertEquals(LeadType::DEAL, $lead2->type->value);
        self::assertEquals(LeadStatus::OTHER_SALES, $lead2->status->value);
        self::assertEquals(LeadType::LEADS, $lead3->type->value);

        // returning customer now should be now on new group id
        $lead4 = Lead::create(
            [
                'type'        => LeadType::LEADS,
                'label'       => 'lead1',
                'customer_id' => $this->customer->id,
                'channel_id'  => $this->channel->id,
                'user_id'     => $this->sales->id,
                'status'      => LeadStatus::GREEN()
            ]
        )->refresh();
        self::assertNotEquals($lead1->refresh()->group_id, $lead4->group_id);
    }

    public function testNewActivityResetLeadStatus(): void
    {
        // new lead with resetable status
        $lead = Lead::factory()->create(['status' => LeadStatus::YELLOW]);
        $this->assertDatabaseHas('leads', ['id' => $lead->id, 'status' => LeadStatus::YELLOW]);

        // add new activity and assert that lead status is reset
        $activity = Activity::factory()->create(['lead_id' => $lead->id]);
        $this->assertDatabaseHas('leads', ['id' => $lead->id, 'status' => LeadStatus::GREEN]);

        // new lead with non-resetable status
        $lead = Lead::factory()->create(['status' => LeadStatus::EXPIRED]);
        $this->assertDatabaseHas('leads', ['id' => $lead->id, 'status' => LeadStatus::EXPIRED]);

        // add new activity and assert that lead status does not reset
        $activity = Activity::factory()->create(['lead_id' => $lead->id]);
        $this->assertDatabaseHas('leads', ['id' => $lead->id, 'status' => LeadStatus::EXPIRED]);
    }
}
