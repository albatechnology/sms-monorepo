<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderTracking;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderTrackingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderTracking::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $type = ['status_update', 'detail_update'];

        return [
            //
            'type'          => $this->faker->randomElement($type),
            'context'       => $this->faker->text($maxNbChars = 50),
            'old_value'     => $this->faker->text($maxNbChars = 50),
            'new_value'     => $this->faker->text($maxNbChars = 50),
            'order_id'      => Order::all()->random()->id,
            'created_at'    => now(),
            'updated_at'    => now(),
        ];
    }
}
