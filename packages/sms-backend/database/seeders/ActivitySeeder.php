<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Lead;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Lead::all()->each(function (Lead $lead){
            Activity::factory()->count(3)->create(
                [
                    "lead_id" => $lead->id,
                ]
            );
        });
    }
}
