<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class OrlansoftApiService
{
    public $http;
    public function __construct()
    {
        $this->http = Http::withBasicAuth(env('ORLANSOFT_API_USERNAME'), env('ORLANSOFT_API_PASSWORD'));
    }

    public function createNewCustomer(Customer $customer): bool
    {
        $customerAddress = $customer?->defaultCustomerAddress ? $customer->defaultCustomerAddress : $customer->customerAddresses->first();
        $customerName = $customer->last_name != '' ? $customer->first_name . ' ' . $customer->last_name : $customer->first_name;

        $GroupA_ID = 10;
        if ($customer->title?->value == 1) {
            $GroupA_ID = 10;
        } elseif ($customer->title?->value == 4) {
            $GroupA_ID = 10;
        } else {
            $GroupA_ID = 11;
        }

        // if ($customer->title == 1) {
        //     $GroupA_ID = 10;
        // } elseif ($customer->title == 4) {
        //     $GroupA_ID = 10;
        // } else {
        //     $GroupA_ID = 11;
        // }

        try {
            $data = DB::connection('orlansoft')->table('ArCustomer')->insert([
                'CustID' => $customer->id,
                'CustName' => $customerName,
                'Address' => $customerAddress->address_line_1,
                'Phones' => $customer->phone,
                'email' => $customer->email,
                'TaxID' => '00-000-000-0-000-000',
                'TaxName' => 'TaxName',
                'TaxAddress' => $customerAddress->address_line_1,
                'City' => $customerAddress->city ?? 'City',
                'State' => $customerAddress->country ?? 'State',
                'CountryID' => 99,
                'Affiliate_' => 0,
                'GL_GroupID' => 'ARGL',
                'ParentID' => 1,
                'BlockShip_' => 0,
                'BlockAR_' => 0,
                'BlockSale_' => 0,
                'DispatchID' => 999,
                'ForwarderID' => 999,
                'AreaID' => '01',
                'CurrencyID' => 'IDR',
                // 'SalesRepID' => $salesRepId,
                'GroupA_ID' => $GroupA_ID,
                'GroupB_ID' => 6,
                'GroupC_ID' => 0,
                'GroupD_ID' => 0,
                'PriceID' => 'HRGJL102',
                'DeliveryMtdID' => 999,
                'FormSetID' => 'DEF',
                'CustStatus' => 0,
                'CreditStatus' => 1,
                'CollectorID' => 999,
                'CreditLimit' => 1,
                'IgnoreCL_' => 1,
                'CreditDays' => 0,
                'TaxGroupID' => 'A1',
                'PKP_' => 0,
                'POSMember_' => 0,
                'Gender' => 1,
                'PersonalID' => time(),
                'GpsLatitude' => 0,
                'GpsLongitude' => 0,
                'Numeric1' => 0,
                'Numeric2' => 0,
                'UseMaxReturnDate' => 0,
                'MaxReturnDate' => 0,
                'AllowMultipleOrder' => 0,
                'MaxUnpaidSO' => 1,
            ]);

            if ($data == 1) {
                $customer->orlan_customer_id = $customer->id;
                $customer->save();

                DB::connection('orlansoft')->table('ARCustTopID')->insert([
                    'TOP_ID' => '01',
                    'CustID' => $customer->id,
                ]);

                return true;
            }
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function cekCustomerById($customer_id)
    {
        $apiUrl = env('ORLANSOFT_API_URL') . '/orlansoft-api/data-access/customer/getCustomer?custId=' . $customer_id;
        $response = $this->http->get($apiUrl);
        if (isset($response['CUSTOMER'])) return true;

        return false;
    }
}
