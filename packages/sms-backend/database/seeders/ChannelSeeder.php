<?php

namespace Database\Seeders;

use App\Models\Channel;
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
        foreach(Company::all() as $company){
            foreach (range(1, 3) as $number) {
                $channelName = 'Channel ' . Str::of($company->name)->after(' ') . $number;
                Channel::factory()->create(["company_id" => $company->id, "name" => $channelName]);
            }
        }
    }
}
