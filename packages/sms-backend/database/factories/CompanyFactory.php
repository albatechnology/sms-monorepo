<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\CompanyAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Company::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'       => $this->faker->company,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function withDefaultAccount()
    {
        return $this->afterCreating(function (Company $company) {
            $account = CompanyAccount::factory()->create(['company_id' => $company->id]);
            $company->update(['company_account_id' => $account->id]);
        });
    }
}
