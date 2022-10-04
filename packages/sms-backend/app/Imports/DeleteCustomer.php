<?php

namespace App\Imports;

use App\Models\Customer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class DeleteCustomer implements ToCollection
{
    public function collection(Collection $rows)
    {
        // dd($rows);
        $success = 0;
        $failed = 0;
        foreach ($rows as $row) {
            $customer = Customer::where('phone', $row[0])->first();
            if ($customer) {
                // dd($customer);
                // $customer->customerLeads()->delete();
                $customer->customerLeads()->each(function ($lead) {
                    $lead->delete();
                });
                // $customer->customerActivity()->delete();
                $customer->customerActivity()->each(function ($activity) {
                    $activity->delete();
                });
                // $customer->customerAddresses()->delete();
                // $customer->customerAddresses()->each(function ($address) {
                //     $address->delete();
                // });
                // // $customer->defaultCustomerAddress()->delete();
                // $customer->defaultCustomerAddress()->each(function ($address) {
                //     $address->delete();
                // });
                // $customer->customerTaxInvoices()->delete();
                $customer->customerTaxInvoices()->each(function ($invoice) {
                    $invoice->delete();
                });
                $customer->delete();
                $success++;
            } else {
                $failed++;
            }
        }
        die('success : ' . $success . ' - Failed : ' . $failed);
    }
}
