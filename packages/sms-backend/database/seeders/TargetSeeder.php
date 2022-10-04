<?php

namespace Database\Seeders;

use App\Models\Target;
use Illuminate\Database\Seeder;

class TargetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Target::factory()->count(30)->create();
    }
}
