<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyAccount;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company = Company::create([
            'name' => 'Alba Digital Technology',
        ]);

        CompanyAccount::create([
            'name' => 'PT. Alba Digital Technology',
            'company_id' => $company->id,
            'bank_name' => 'BCA',
            'account_name' => 'PT. Alba Digital Technology',
            'account_number' => '00394851101',
        ]);
    }
}
