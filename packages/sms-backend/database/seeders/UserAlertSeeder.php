<?php

namespace Database\Seeders;

use App\Models\UserAlert;
use Illuminate\Database\Seeder;

class UserAlertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserAlert::factory()->count(20)->create();
    }
}
