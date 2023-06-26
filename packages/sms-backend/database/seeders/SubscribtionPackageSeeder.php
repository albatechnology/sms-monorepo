<?php

namespace Database\Seeders;

use App\Models\SubscribtionPackage;
use App\Models\SubscribtionUser;
use Illuminate\Database\Seeder;

class SubscribtionPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SubscribtionUser::create([
            'name' => 'PT. Alba Digital Technology',
            'email' => 'alba@gmail.com',
            'phone' => '080808080808',
        ]);

        $starter = SubscribtionPackage::create([
            'name' => 'Starter',
            'max_users' => 10,
            'max_customers' => 500,
            'max_activities' => 500,
            'max_leads' => 500,
            'max_orders' => 1000,
            'max_brands' => 1,
            'max_categories' => 10,
            'max_products' => 50,
            'can_discount' => 0,
            'can_approval' => 0,
            'can_multi_companies' => 0,
        ]);

        $starter->subscribtionUsers()->create([
            'name' => 'User Starter',
            'email' => 'user.starter@gmail.com',
            'phone' => '09876543211',
            'expiration_date' => date('Y-m-d', strtotime('+1 month')),
        ]);

        $basic = SubscribtionPackage::create([
            'name' => 'Basic',
            'max_users' => 50,
            'max_customers' => 3000,
            'max_activities' => 2000,
            'max_leads' => 3000,
            'max_orders' => 10000,
            'max_brands' => 10,
            'max_categories' => 50,
            'max_products' => 500,
            'can_discount' => 1,
            'can_approval' => 1,
            'can_multi_companies' => 0,
        ]);

        $basic->subscribtionUsers()->create([
            'name' => 'User Basic',
            'email' => 'user.basic@gmail.com',
            'phone' => '09876789009',
            'expiration_date' => date('Y-m-d', strtotime('+1 month')),
        ]);

        $advance = SubscribtionPackage::create([
            'name' => 'Advance',
            'max_users' => 200,
            'max_customers' => null,
            'max_activities' => null,
            'max_leads' => null,
            'max_orders' => null,
            'max_brands' => null,
            'max_categories' => null,
            'max_products' => 5000,
            'can_discount' => 1,
            'can_approval' => 1,
            'can_multi_companies' => 1,
        ]);

        $advance->subscribtionUsers()->create([
            'name' => 'User Advance',
            'email' => 'user.advance@gmail.com',
            'phone' => '098123456734',
            'expiration_date' => date('Y-m-d', strtotime('+1 month')),
        ]);
    }
}
