<?php

namespace Database\Seeders;

use App\Models\Colour;
use App\Models\ProductBrand;
use Illuminate\Database\Seeder;

class ColourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductBrand::all()->each(function (ProductBrand $productBrand){
            Colour::factory()->count(2)->create([
                'product_brand_id' => $productBrand->id
            ]);
        });
    }
}
