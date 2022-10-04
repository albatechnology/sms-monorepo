<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $order       = Order::factory()->create();
        $paymentType = PaymentType::factory()->create(['company_id' => $order->company_id]);

        return [
            //
            'amount'          => $this->faker->randomNumber(5),
            'reference'       => $this->faker->text($maxNbChars = 50),
            'status'          => PaymentStatus::APPROVED,
            'payment_type_id' => $paymentType->id,
            'order_id'        => $order->id,
            'company_id'      => $order->company_id,
            'approved_by_id'  => User::all()->random()->id,
            'added_by_id'     => User::all()->random()->id,
            'created_at'      => now(),
            'updated_at'      => now()
        ];
    }

    public function forOrderSettlement(Order $order)
    {
        return $this->state(
            [
                'company_id' => $order->company_id,
                'order_id'   => $order->id,
                'amount'     => $order->total_price - $order->amount_paid,
            ]
        );
    }
}
