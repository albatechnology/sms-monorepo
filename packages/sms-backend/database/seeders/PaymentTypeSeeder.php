<?php

namespace Database\Seeders;

use App\Models\PaymentCategory;
use App\Models\PaymentType;
use Illuminate\Database\Seeder;

class PaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentCategory::all()->each(function (PaymentCategory $category) {
            $names = collect(['Bank BCA', 'Bank BNI', 'Bank  CIMB', 'Bank Mandiri', 'Bank Permata'])
                ->map(fn($q) => [
                    'name'                => $q,
                    'company_id'          => $category->company_id,
                    'payment_category_id' => $category->id
                ])
                ->all();

            PaymentType::insert($names);
        });
    }
}
