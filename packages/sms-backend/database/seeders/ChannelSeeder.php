<?php

namespace Database\Seeders;

use App\Models\Channel;
use Illuminate\Database\Seeder;

class ChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $company = Company::create([
        //     'name' => 'Company 1',
        // ]);

        Channel::create([
            // 'company_id' => $company->id,
            'subscribtion_user_id' => 2,
            'name' => 'Channel 1'
        ]);
    }
}
