<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Customer::factory()->count(50)->create();
        $customer = Customer::create([
            'title' => 1,
            'first_name' => 'Customer satu',
            'last_name' => 'OKE',
            'email' => 'customer@gmail.com',
            'phone' => '0987654321',
            'date_of_birth' => date('Y-m-d'),
            'description' => 'description customer',
        ]);

        Address::create([
            'customer_id' => $customer->id,
            'address_line_1' => 'Tangerang Raya',
            'postcode' => '15710',
            'city' => 'Tangerang',
            'country' => 'Indonesia',
            'province' => 'Banten',
            'type' => 1,
            'phone' => '0987654321',
        ]);
    }
}
