<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductList;
use App\Models\ProductTag;
use Illuminate\Database\Seeder;

class ProductListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $product = Product::first() ?? Product::factory()->create();

        // Create product brand list
        (new ProductList())->model()->associate($product->brand)->save();

        // Create product model list
        (new ProductList())->model()->associate($product->model)->save();

        // Create custom product list
        $products = Product::limit(5)->get('id');
        $products = !$products->isEmpty() ? $products : Product::factory()->count(5)->create();
        ProductList::create(['product_ids' => $products->pluck('id')]);

        // Create Category product list (not nested)
        $child_category = ProductCategory::hasParent()->first();
        (new ProductList())->model()->associate($child_category)->save();

        // Create nested category product list
        (new ProductList())->model()->associate($child_category->ancestors->first())->save();

        // Create product tag category list
        (new ProductList())->model()->associate(ProductTag::first())->save();


    }
}
