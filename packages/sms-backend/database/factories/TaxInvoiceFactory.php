<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Customer;
use App\Models\TaxInvoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaxInvoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TaxInvoice::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'company_name'  => $this->faker->company,
            'npwp'          => $this->faker->numberBetween(0000000000, 9999999999),
            'email'         => $this->faker->safeEmail,
            'customer_id'   => Customer::all()->random()->id,
            'address_id'    => Address::all()->random()->id,
            'created_at'    => now(),
            'updated_at'    => now(),
        ];
    }
}
