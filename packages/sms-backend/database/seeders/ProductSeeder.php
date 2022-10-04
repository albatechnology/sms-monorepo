<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategoryCode;
use App\Models\ProductModel;
use App\Models\ProductTag;
use App\Models\ProductVersion;
use App\Services\HelperService;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public $product_count = 0;
    public $model_count = 0;
    public $product_names = [];
    public $model_names = [];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags    = ProductTag::all();
        $company = Company::first();

        $data['ProductTag'] = $tags;
        $data['Company']    = $company;

        // use sample product names
        $this->product_names = HelperService::loadSampleData(HelperService::PRODUCT_NAMES);
        $this->model_names   = HelperService::loadSampleData(HelperService::MODEL_NAMES);

        ProductBrand::factory()->count(2)->create()->each(function (ProductBrand $brand) use ($data) {

            $data['ProductBrand'] = $brand;

            $models = ProductModel::factory()
                ->count(2)
                ->state(new Sequence(
                    ['name' => $this->model_names[$this->model_count]],
                    ['name' => $this->model_names[$this->model_count + 1]],
                ))
                ->create();

            $this->model_count = $this->model_count + 2;

            $models->each(function (ProductModel $model) use ($data) {
                $data['ProductModel'] = $model;

                ProductVersion::factory()->count(2)->create()->each(function (ProductVersion $version) use ($data) {
                    $data['ProductVersion'] = $version;

                    ProductCategoryCode::factory()->count(2)->create()->each(function (ProductCategoryCode $code) use ($data) {
                        Product::factory()
                            ->withProductUnits()
                            ->withTags($data['ProductTag']->random(3))
                            ->create([
                                'name'                     => $this->product_names[$this->product_count],
                                'company_id'               => $data['Company']->id,
                                'product_brand_id'         => $data['ProductBrand']->id,
                                'product_model_id'         => $data['ProductModel']->id,
                                'product_version_id'       => $data['ProductVersion']->id,
                                'product_category_code_id' => $code->id,
                            ]);

                        $this->product_count++;
                    });
                });
            });
        });
    }
}
