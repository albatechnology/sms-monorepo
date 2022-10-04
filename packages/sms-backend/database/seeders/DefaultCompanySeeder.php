<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class DefaultCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Company::count() == 0) {
            Company::factory()->withDefaultAccount()->create(["name" => 'Head office']);
        }
    }
}
