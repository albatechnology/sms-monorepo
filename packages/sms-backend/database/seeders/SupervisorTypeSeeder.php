<?php

namespace Database\Seeders;

use App\Models\SupervisorType;
use Illuminate\Database\Seeder;

class SupervisorTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect(
            [
                [
                    'name'  => 'Store Leader',
                    'code'  => 'store-leader',
                    'level' => 1,
                ],
                [
                    'name'  => 'Manager Area',
                    'code'  => 'manager-area',
                    'level' => 2,
                ],
                [
                    'name'  => 'Head Sales',
                    'code'  => 'head-sales',
                    'level' => 3,
                ],
                [
                    'name'  => 'Director Sales Marketing',
                    'code'  => 'director-sales-marketing',
                    'level' => 4,
                ],
            ]
        )->each(function ($data) {
            SupervisorType::firstOrCreate(
                [
                    'code' => $data['code']
                ],
                [
                    'name'  => $data['name'],
                    'level' => $data['level'],
                ]
            );
        });
    }
}
