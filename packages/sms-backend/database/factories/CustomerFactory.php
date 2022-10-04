<?php

namespace Database\Factories;

use App\Enums\PersonTitle;
use App\Models\Address;
use App\Models\Customer;

class CustomerFactory extends BaseFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title'       => PersonTitle::getRandomValue(),
            'first_name'  => $this->faker->firstName,
            'last_name'   => $this->faker->lastName,
            'email'       => $this->faker->safeEmail,
            'phone'       => '08' . $this->faker->numberBetween(1000000, 100000000000),
            'description' => $this->faker->text($maxNbChars = 100),
            'created_at'  => now(),
            'updated_at'  => now(),
        ];
    }


    public function withAddress()
    {
        return $this->afterCreating(function (Customer $customer) {
            $address = Address::factory()->create(['customer_id' => $customer->id]);
            $customer->update(["default_address_id" => $address->id]);
        });
    }
}
