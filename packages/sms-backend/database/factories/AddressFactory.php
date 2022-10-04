<?php

namespace Database\Factories;

use App\Enums\AddressType;
use App\Models\Address;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Address::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'address_line_1' => $this->faker->streetAddress,
            'address_line_2' => $this->faker->streetAddress,
            'address_line_3' => null,
            'postcode'       => $this->faker->postcode,
            'city'           => $this->faker->city,
            'country'        => $this->faker->country,
            'province'       => $this->faker->state,
            'type'           => AddressType::getRandomValue(),
            'phone'          => '08123456898319',
            'customer_id'    => Customer::first()->id ?? Customer::factory()->create()->id,
            'created_at'     => now(),
            'updated_at'     => now(),
        ];
    }
}
