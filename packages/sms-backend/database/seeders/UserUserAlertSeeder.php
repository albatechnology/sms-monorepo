<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserAlert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserUserAlertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 0; $i < 100; $i++){
            DB::table('user_user_alert')->insert([
                'user_alert_id' => UserAlert::all()->random()->id,
                'user_id' => User::all()->whereNotNull('type')->random()->id,
                'read' => rand(0,1)
            ]);
        }
    }
}
