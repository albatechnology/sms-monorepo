<?php

namespace Database\Seeders;

use App\Enums\TargetType;
use App\Models\TargetTypePriority;
use Illuminate\Database\Seeder;

class TargetTypePrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (TargetType::getInstances() as $enum) {
            TargetTypePriority::updateOrCreate(
                [
                    'target_type' => $enum->value,
                ],
                [
                    'priority' => $enum->getPriority()
                ]
            );
        }
    }
}
