<?php

namespace Database\Seeders;

use App\Models\BrandCategory;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $brandCategory = BrandCategory::create([
            'name' => 'Brand Category 1',
            'code' => 'BC1',
            'slug' => 'brand-category-1',
        ]);

        $productBrand = ProductBrand::create([
            'brand_category_id' => $brandCategory->id,
            'name' => 'Product Brand 1'
        ]);

        $productCategory = ProductCategory::create([
            'name' => 'Product Category 1'
        ]);

        Product::create([
            'product_category_id' => $productCategory->id,
            'product_brand_id' => $productBrand->id,
            'brand_category_id' => $brandCategory->id,
            'name' => 'Product 1',
            'description' => 'Description Product 1',
            'sku' => '001',
            'price' => 1000000,
            'is_active' => 1,
            'uom' => 1,
            'production_cost' => 500000,
        ]);
    }
}
