<?php

namespace Database\Factories;

use App\Models\Stock;

class StockFactory extends BaseFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Stock::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [

        ];
//        return [
//            'name'                     => $this->faker->productName,
//            'is_active'                => $this->faker->numberBetween(0, 1),
//            'price'                    => $this->faker->randomNumber(3) * 100000,
//            'company_id'               => Company::first()->id ?? Company::factory()->create()->id,
//            'product_unit_id'         => ProductBrand::first()->id ?? ProductBrand::factory()->create()->id,
//            'product_model_id'         => ProductModel::first()->id ?? ProductModel::factory()->create()->id,
//            'product_version_id'       => ProductVersion::first()->id ?? ProductVersion::factory()->create()->id,
//            'product_category_code_id' => ProductCategoryCode::factory()->create()->id,
//        ];
    }
}
