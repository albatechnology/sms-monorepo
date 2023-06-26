<?php

namespace Database\Seeders;

use App\Models\PaymentCategory;
use App\Models\PaymentType;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $paymentCategory = PaymentCategory::create([
            'subscribtion_user_id' => 2,
            'name' => 'Transfer'
        ]);

        PaymentType::create([
            'subscribtion_user_id' => 2,
            'payment_category_id' => $paymentCategory->id,
            'name' => 'BCA Prioritas',
        ]);
        // Payment::factory()->count(15)->create();
    }
}
