<?php

namespace Database\Seeders;

use App\Models\ProductUnit;
use App\Models\Promo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductUnitPromoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        for($i = 0; $i < 100; $i++){
            DB::table('product_unit_promo')->insert([
                'promo_id' => Promo::all()->random()->id,
                'product_unit_id' => ProductUnit::all()->random()->id
            ]);
        }
    }
}
