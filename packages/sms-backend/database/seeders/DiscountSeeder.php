<?php

namespace Database\Seeders;

use App\Enums\DiscountScope;
use App\Enums\DiscountType;
use App\Models\Discount;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Discount::factory()->create(
            [
                'description' => '50% Discount All Products (per transaction)',
            ]
        );

        Discount::factory()->create(
            [
                'description' => '50% Discount All Products (per type)',
                'value'       => 50,
                'scope'       => DiscountScope::TYPE,
            ]
        );

        Discount::factory()->create(
            [
                'description' => '50% Discount All Products (per quantity)',
                'value'       => 50,
                'scope'       => DiscountScope::QUANTITY,
            ]
        );

        Discount::factory()->create(
            [
                'description'     => '80% Discount All Products (hidden)',
                'value'           => 80,
                'activation_code' => 'TESTCODE'
            ]
        );

        Discount::factory()->create(
            [
                'description' => '100% Discount All Products (inactive)',
                'value'       => 100000,
                'is_active'   => 0
            ]
        );

        Discount::factory()->create(
            [
                'description' => '100000 nominal (per transaction)',
                'value'       => 100000,
                'type'        => DiscountType::NOMINAL,
            ]
        );

        Discount::factory()->create(
            [
                'description' => '100000 nominal All Products (per type)',
                'value'       => 100000,
                'scope'       => DiscountScope::TYPE,
                'type'        => DiscountType::NOMINAL,
            ]
        );

        Discount::factory()->create(
            [
                'description' => '100000 nominal All Products (per quantity)',
                'value'       => 100000,
                'scope'       => DiscountScope::QUANTITY,
                'type'        => DiscountType::NOMINAL,
            ]
        );
    }
}
