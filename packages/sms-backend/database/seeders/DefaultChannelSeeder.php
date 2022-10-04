<?php

namespace Database\Seeders;

use App\Models\Channel;
use Illuminate\Database\Seeder;

class DefaultChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Channel::count() == 0) {
            Channel::factory()->create(['company_id' => 1, 'name' => 'Default']);
        }
    }
}
