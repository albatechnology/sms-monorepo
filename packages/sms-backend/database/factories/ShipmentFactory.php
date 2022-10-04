<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ShipmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Shipment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $status = ['waiting', 'dispatched', 'arrived', 'cancelled'];

        return [
            'status'                    => $this->faker->randomElement($status),
            'note'                      => $this->faker->text(20),
            'reference'                 => $this->faker->text(20),
            'estimated_delivery_date'   => Carbon::now()->subDays(30)->format('Y-m-d'),
            'shipment_time'             => Carbon::now()->addDays(30),
            'order_id'                  => Order::all()->random()->id,
            'fulfilled_by_id'           => User::all()->random()->id,
            'created_at'                => now(),
            'updated_at'                => now()
        ];
    }
}
