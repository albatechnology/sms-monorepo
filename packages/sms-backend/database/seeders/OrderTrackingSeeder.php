<?php

namespace Database\Seeders;

use App\Models\OrderTracking;
use Illuminate\Database\Seeder;

class OrderTrackingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        OrderTracking::factory()->count(25)->create();
    }
}
