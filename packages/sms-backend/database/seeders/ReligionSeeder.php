<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Religion;

class ReligionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $religions = [
            'Islam',
            'Kristen',
            'Katolik',
            'Hindu',
            'Buddha',
            'Kong Hu Cu',
            'Lainnya'
        ];

        foreach($religions as $religion){
            Religion::create([
                'name' => $religion
            ]);
        }
    }
}
