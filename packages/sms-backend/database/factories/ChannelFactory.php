<?php

namespace Database\Factories;

use App\Models\Channel;
use App\Models\ChannelCategory;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChannelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Channel::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
            'name'                => $this->faker->company,
            'channel_category_id' => ChannelCategory::first()->id ?? ChannelCategory::factory()->create()->id,
            'company_id'          => Company::first()->id ?? Company::factory()->create()->id,
            'created_at'          => now(),
            'updated_at'          => now(),
        ];
    }

    public function forCompany(?Company $company): ChannelFactory
    {
        return $this->state(
            [
                "company_id" => $company ? $company->id : Company::factory(),
            ]
        );
    }

    public function forCategory(?ChannelCategory $category): ChannelFactory
    {
        return $this->state(
            [
                "channel_category_id" => $category ? $category->id : ChannelCategory::factory(),
            ]
        );
    }

    public function withCategory()
    {
        return $this->afterCreating(function (Channel $channel) {
            $category = ChannelCategory::factory()->create();
            $channel->channelCategory()->associate($category);
        });
    }
}
