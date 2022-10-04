<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\CompanyAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyAccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CompanyAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'           => 'default',
            'bank_name'      => $this->faker->randomElement(['BCA', 'BNI', 'Mandiri']),
            'account_name'   => 'Test Account',
            'account_number' => '123123123',
            'company_id'     => Company::first()->id ?? Company::factory()->create()->id,
            'created_at'     => now(),
            'updated_at'     => now(),
        ];
    }
}
