<?php

namespace Database\Seeders;

use App\Models\Covering;
use Illuminate\Database\Seeder;

class CoveringSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Covering::factory()->count(3)->create();
    }
}
