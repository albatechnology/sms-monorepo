<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\Lead;
use App\Models\LeadCategory;
use App\Models\SubLeadCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $leadCategory = LeadCategory::create([
            'name' => 'Lead Category 1',
            'description' => 'description Lead Category 1',
        ]);


        SubLeadCategory::create([
            'lead_category_id' => $leadCategory->id,
            'name' => 'Lead Category 1',
            'description' => 'description Lead Category 1',
        ]);
        // $code = 'L';

        // foreach(Channel::all() as $channel){
        //     foreach (range(1, 3) as $number) {
        //         $name = sprintf(
        //             'Lead %s-%s%s',
        //             Str::of($channel->name)->after(' '),
        //             $code,
        //             $number
        //         );
        //         Lead::factory()->create(
        //             [
        //                 "channel_id" => $channel->id,
        //                 "name" => $name,
        //             ]
        //         );
        //     }
        // }
    }
}
