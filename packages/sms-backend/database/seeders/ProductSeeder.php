<?php

namespace Database\Seeders;

use App\Imports\ProductSeederImport;
use App\Models\BrandCategory;
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
        $productBrandOtomotif->addMediaFromUrl('https://melandas-production.s3.ap-southeast-1.amazonaws.com/39/64ed725a735e5_logo-astra-otoparts.jpg')->toMediaCollection('photo');

        $productCategoryOtomotif = ProductCategory::create([
            'subscribtion_user_id' => 2,
            'name' => 'Category Otomotif'
        ]);
        $productCategoryOtomotif->addMediaFromUrl('https://melandas-production.s3.ap-southeast-1.amazonaws.com/36/64ed691f3d78e_Screen-Shot-2023-08-29-at-10.38.49.png')->toMediaCollection('photo');
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
        $productBrandProperty->addMediaFromUrl('https://melandas-production.s3.ap-southeast-1.amazonaws.com/40/64ed7329ae0c8_pngtree-property-logo-png-image_6430110.png')->toMediaCollection('photo');

        $productCategoryProperty = ProductCategory::create([
            'subscribtion_user_id' => 2,
            'name' => 'Category Properti'
        ]);
        $productCategoryProperty->addMediaFromUrl('https://melandas-production.s3.ap-southeast-1.amazonaws.com/35/64ed6915ead8f_Screen-Shot-2023-08-29-at-10.36.35.png')->toMediaCollection('photo');
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
        $productBrandAssurance->addMediaFromUrl('https://melandas-production.s3.ap-southeast-1.amazonaws.com/41/64ed7448c8ccc_256x256bb.jpg')->toMediaCollection('photo');

        $productCategoryAssurance = ProductCategory::create([
            'subscribtion_user_id' => 2,
            'name' => 'Category Assurance'
        ]);
        $productCategoryAssurance->addMediaFromUrl('https://melandas-production.s3.ap-southeast-1.amazonaws.com/34/64ed6904a42e3_logo-about-us.png')->toMediaCollection('photo');
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
