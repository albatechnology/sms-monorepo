<?php

namespace App\Imports;

use App\Models\BrandCategory;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\SubscribtionUser;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductSeederImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $productName = trim($row['name'] ?? '-');
        $productDescription = trim($row['description'] ?? $productName);
        $sku = trim($row['sku'] ?? '-');

        if (Product::where('name', $productName)->exists()) return;

        $subscriptionUserId = SubscribtionUser::find($row['subscribtion_user_id'])?->id ?? 1;
        $productCategoryId = ProductCategory::find($row['product_category_id'])?->id ?? 1;
        $productBrandId = ProductBrand::find($row['product_brand_id'])?->id ?? 1;
        $brandCategoryId = BrandCategory::find($row['brand_category_id'])?->id ?? 1;

        $product = Product::create([
            'subscribtion_user_id' => $subscriptionUserId,
            'product_category_id' => $productCategoryId,
            'product_brand_id' => $productBrandId,
            'brand_category_id' => $brandCategoryId,
            'name' => $productName,
            'description' => $productDescription,
            'sku' => $sku,
            'price' => $row['price'] ?? 0,
            'production_cost' => $row['production_cost'] ?? 0,
            'is_active' => 1,
        ]);

        if (isset($row['url']) && $row['url'] != '') $product->addMediaFromUrl($row['url'])->toMediaCollection('photo');
        return;
    }
}
