<?php

namespace Database\Factories;

use App\Models\Media;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class MediaFactory extends Factory
{
    protected $model = Media::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'model_type'            => Product::class,
            'model_id'              => $this->faker->randomNumber(),
            'order_column'          => $this->faker->randomNumber(),
            'name'                  => 'test_image',
            'responsive_images'     => [],
            'file_name'             => 'test_image.png',
            'generated_conversions' => ["thumb" => true, "preview" => true],
            'mime_type'             => 'image/png',
            'manipulations'         => [],
            'uuid'                  => $this->faker->uuid,
            'collection_name'       => 'image',
            'disk'                  => 'public',
            'custom_properties'     => [],
            'conversions_disk'      => 'public',
            'size'                  => 1132,
            'updated_at'            => Carbon::now(),
            'created_at'            => Carbon::now(),
        ];
    }

    public function model(Model $model)
    {
        return $this->state(function (array $attributes) use ($model){
            return [
                'model_type' => $model::class,
                'model_id' => $model->id,
            ];
        });
    }
}
