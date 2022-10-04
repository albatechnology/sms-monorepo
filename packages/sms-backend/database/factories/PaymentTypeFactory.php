<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Order;
use App\Models\PaymentCategory;
use App\Models\PaymentType;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PaymentType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $company = Company::first() ?? Company::factory()->create();

        return [
            'name'                => $this->faker->name,
            'require_approval'    => $this->faker->numberBetween(0, 1),
            'payment_category_id' => PaymentCategory::where(['company_id' => $company->id])->first()?->id ??
                PaymentCategory::factory()
                    ->create(['company_id' => $company->id])
                    ->id,
            'company_id'          => $company->id,
            'created_at'          => now(),
            'updated_at'          => now(),
        ];
    }

    public function forOrder(Order $order)
    {
        $category = PaymentCategory::factory()->create(['company_id' => $order->company_id]);
        return $this->state(
            [
                'payment_category_id' => $category->id,
                'company_id'          => $order->company_id,
            ]
        );
    }
}
