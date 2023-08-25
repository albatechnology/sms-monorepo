<?php

namespace Database\Seeders;

use App\Imports\ProductSeederImport;
use App\Models\BrandCategory;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // otomotif
        $brandCategoryOtomotif = BrandCategory::create([
            'subscribtion_user_id' => 2,
            'name' => 'Brand Category Otomotif',
            'code' => 'BCOtomotif',
            'slug' => 'brand-category-Otomotif',
        ]);

        $productBrandOtomotif = ProductBrand::create([
            'subscribtion_user_id' => 2,
            'brand_category_id' => $brandCategoryOtomotif->id,
            'name' => 'Brand Otomotif'
        ]);

        $productCategoryOtomotif = ProductCategory::create([
            'subscribtion_user_id' => 2,
            'name' => 'Category Otomotif'
        ]);
        // otomotif

        //property
        $brandCategoryProperty = BrandCategory::create([
            'subscribtion_user_id' => 2,
            'name' => 'Brand Category Properti',
            'code' => 'BCProperti',
            'slug' => 'brand-category-Properti',
        ]);

        $productBrandProperty = ProductBrand::create([
            'subscribtion_user_id' => 2,
            'brand_category_id' => $brandCategoryProperty->id,
            'name' => 'Brand Properti'
        ]);

        $productCategoryProperty = ProductCategory::create([
            'subscribtion_user_id' => 2,
            'name' => 'Category Properti'
        ]);
        //property

        // asuransi
        $brandCategoryAssurance = BrandCategory::create([
            'subscribtion_user_id' => 2,
            'name' => 'Brand Category Assurance',
            'code' => 'ACassurance',
            'slug' => 'brand-category-Assurance',
        ]);

        $productBrandAssurance = ProductBrand::create([
            'subscribtion_user_id' => 2,
            'brand_category_id' => $brandCategoryAssurance->id,
            'name' => 'Brand Assurance'
        ]);

        $productCategoryAssurance = ProductCategory::create([
            'subscribtion_user_id' => 2,
            'name' => 'Category Assurance'
        ]);
        // asuransi


        Excel::import(new ProductSeederImport, public_path('products.xlsx'));

        // Product::create([
        //     'subscribtion_user_id' => 2,
        //     'product_category_id' => $productCategory->id,
        //     'product_brand_id' => $productBrand->id,
        //     'brand_category_id' => $brandCategory->id,
        //     'name' => 'Product 1',
        //     'description' => 'Description Product 1',
        //     'sku' => '001',
        //     'price' => 1000000,
        //     'is_active' => 1,
        //     'uom' => 1,
        //     'production_cost' => 500000,
        // ]);
    }
}
