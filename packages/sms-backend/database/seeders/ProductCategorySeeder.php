<?php

namespace Database\Seeders;

use App\Enums\ProductCategoryType;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    public function run()
    {
        ProductCategory::factory()
                       ->count(2)
                       ->create(
                           [
                               "type" => ProductCategoryType::COLLECTION
                           ]
                       )
                       ->each(function (ProductCategory $category) {
                           ProductCategory::factory()->count(3)->create(
                               [
                                   "type"      => ProductCategoryType::SUB_COLLECTION,
                                   "parent_id" => $category->id
                               ]
                           );
                       });

        ProductCategory::factory()->count(2)->create(
            [
                "type" => ProductCategoryType::BRAND_TYPE
            ]
        )->each(function (ProductCategory $category) {
            ProductCategory::factory()->count(3)->create(
                [
                    "type"      => ProductCategoryType::BRAND,
                    "parent_id" => $category->id
                ]
            );
        });

        ProductCategory::factory()->count(3)->create(
            [
                "type" => ProductCategoryType::CATEGORY
            ]
        );
    }
}
