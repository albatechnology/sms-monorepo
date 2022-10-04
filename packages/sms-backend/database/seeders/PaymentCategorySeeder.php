<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\PaymentCategory;
use Illuminate\Database\Seeder;

class PaymentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company_id = Company::first()->id;
        $names      = collect(['Transfer', 'Virtual Account', 'Credit Card', 'Debit Card'])
            ->map(fn($q) => ['name' => $q, 'company_id' => $company_id])
            ->all();

        PaymentCategory::insert($names);
    }
}
