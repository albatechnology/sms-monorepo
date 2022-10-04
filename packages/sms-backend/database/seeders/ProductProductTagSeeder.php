<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductProductTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 0; $i < 100; $i++){
            DB::table('product_product_tag')->insert([
                'product_id' => Product::all()->random()->id,
                'product_tag_id' => ProductTag::all()->random()->id
            ]);
        }
    }
}
