<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\ChannelCategory;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

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
            'name' => 'Channel 1'
        ]);
    }
}
