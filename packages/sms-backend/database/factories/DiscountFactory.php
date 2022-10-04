<?php

namespace Database\Factories;

use App\Enums\DiscountScope;
use App\Enums\DiscountType;
use App\Models\Company;
use App\Models\Discount;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiscountFactory extends Factory
{
    protected $model = Discount::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'            => $this->faker->sentence,
            'description'     => $this->faker->text,
            'type'            => DiscountType::PERCENTAGE,
            'scope'           => DiscountScope::TRANSACTION,
            'activation_code' => null,
            'value'           => 50,
            'start_time'      => now()->subMonth(),
            'is_active'       => 1,
            'company_id'      => Company::first()?->id ?? function () {
                    return Company::factory()->create()->id;
                },

        ];
    }

    public function nominal()
    {
        return $this->state(
            [
                'type' => DiscountType::NOMINAL,
            ]
        );
    }

    public function percentage()
    {
        return $this->state(
            [
                'type' => DiscountType::PERCENTAGE,
            ]
        );
    }

    public function scopeQuantity()
    {
        return $this->state(
            [
                'scope' => DiscountScope::QUANTITY,
            ]
        );
    }

    public function scopeType()
    {
        return $this->state(
            [
                'scope' => DiscountScope::TYPE,
            ]
        );
    }

    public function value($int)
    {
        return $this->state(
            [
                'value' => $int,
            ]
        );
    }
}
