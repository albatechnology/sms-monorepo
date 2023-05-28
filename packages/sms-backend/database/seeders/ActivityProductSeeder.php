<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 0; $i < 200; $i++){
            DB::table('activity_product')->insert([
                'activity_id' => Activity::all()->random()->id,
                'product_id' => Product::all()->random()->id
            ]);
        }
    }
}
