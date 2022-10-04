<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Discount;
use App\Models\Promo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PromoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Promo::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $company = Company::first()->id ?? Company::factory()->create()->id;
        return [
            'name'        => $this->faker->name,
            'description' => $this->faker->text(50),
            'start_time'  => Carbon::now()->subDays(15),
            'end_time'    => Carbon::now()->addDays(15),
            'discount_id' => Discount::first()->id ?? Discount::factory()->create()->id,
            'company_id'  => Company::first()->id ?? Company::factory()->create()->id,
            'created_at'  => now(),
            'updated_at'  => now(),
        ];
    }
}
