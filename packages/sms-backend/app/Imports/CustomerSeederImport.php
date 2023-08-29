<?php

namespace App\Imports;

use App\Models\Address;
use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerSeederImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $customer = Customer::create([
            'subscribtion_user_id' => $row['subscribtion_user_id'],
            'title' => $row['title'],
            'first_name' => trim($row['first_name']),
            'last_name' => trim($row['last_name']),
            'email' => trim($row['email']),
            'phone' => trim($row['phone']),
        ]);

        Address::create([
            'customer_id' => $customer->id,
            'postcode' => trim($row['postcode']),
            'city' => trim($row['city']),
            'province' => trim($row['province']),
            'country' => trim($row['country']),
            'address_line_1' => trim($row['address_line_1']),
            'phone' => trim($row['phone']),
            'type' => 1,
        ]);

        return;
    }
}
