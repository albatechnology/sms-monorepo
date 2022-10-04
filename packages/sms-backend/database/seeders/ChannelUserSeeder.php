<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChannelUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        for($i = 0; $i < 150; $i++){
            DB::table('channel_user')->insert([
                'user_id' => User::all()->random()->id,
                'channel_id' => Channel::all()->random()->id
            ]);
        }
    }
}
