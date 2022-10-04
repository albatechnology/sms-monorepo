<?php

namespace Database\Factories;

use App\Classes\CartItem;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartFactory extends Factory
{
    protected $model = Cart::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id'     => User::first()->id ?? User::factory()->create()->id,
            'customer_id' => Customer::first()->id ?? Customer::factory()->create()->id,
            'items'       => CartItem::factory()->make(),
        ];
    }
}
