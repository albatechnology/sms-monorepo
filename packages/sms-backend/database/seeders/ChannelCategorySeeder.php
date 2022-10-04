<?php

namespace Database\Seeders;

use App\Models\ChannelCategory;
use Illuminate\Database\Seeder;

class ChannelCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        ChannelCategory::factory()->count(20)->create();
    }
}
