<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\Lead;
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
        $code = 'L';

        foreach(Channel::all() as $channel){
            foreach (range(1, 3) as $number) {
                $name = sprintf(
                    'Lead %s-%s%s',
                    Str::of($channel->name)->after(' '),
                    $code,
                    $number
                );
                Lead::factory()->create(
                    [
                        "channel_id" => $channel->id,
                        "name" => $name,
                    ]
                );
            }
        }
    }
}
