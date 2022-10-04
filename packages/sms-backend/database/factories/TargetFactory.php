<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\Target;
use Illuminate\Database\Eloquent\Factories\Factory;

class TargetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Target::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'value'      => 0,
            'target'     => 0,
            'type'       => $this->faker->randomElement(['channel', 'sales', 'category']),
            'model_id'   => $this->faker->text(10),
            'model_type' => $this->faker->text(10),
            'report_id'  => Report::factory()->create()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function forReport(Report $report)
    {
        return $this->state([
            'report_id' => $report->id
        ]);
    }
}
