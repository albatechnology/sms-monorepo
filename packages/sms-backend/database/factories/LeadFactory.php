<?php

namespace Database\Factories;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Models\Channel;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeadFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Lead::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type'             => LeadType::LEADS,
            'status'           => LeadStatus::GREEN,
            'is_new_customer'  => $this->faker->numberBetween(0, 1),
            'label'            => $this->faker->domainWord,
            'customer_id'      => Customer::first()->id ?? Customer::factory()->withAddress()->create()->id,
            'lead_category_id' => LeadCategory::first()->id ?? LeadCategory::factory()->create()->id,
            'user_id'          => User::whereIsSales()->first()->id ?? User::factory()->sales()->create()->id,
            'created_at'       => now(),
            'updated_at'       => now(),
        ];
    }

    public function sample()
    {
        return $this->state([
            'label',
            'lead_category_id',
            'sub_lead_category_id',
            'customer_id',

            'name_title_id',
            'first_name',
            'last_name',
            'date_of_birth',
            'email',
            'phone',
            'description',
            'address_line_1',
            'address_line_2',
            'address_line_3',

            'country',
            'province',
            'city',
            'postcode',
            'address_type',
            'address_phone',
        ]);
    }
}
