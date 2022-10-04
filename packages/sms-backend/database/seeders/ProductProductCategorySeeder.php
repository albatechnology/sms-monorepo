<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductProductCategorySeeder extends Seeder
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
            DB::table('product_product_category')->insert([
                'product_id' => Product::all()->random()->id,
                'product_category_id' => ProductCategory::all()->random()->id
            ]);
        }
    }
}
