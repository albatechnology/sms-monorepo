<?php

namespace App\Http\Requests\API\V1\Customer;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\RequestData;
use App\Http\Requests\API\V1\CreateAddressRequest;
use App\Models\Address;
use App\Models\Customer;

class CreateCustomerWithAddressRequest extends BaseApiRequest
{
    public function getEnumCasts()
    {
        return array_merge(Customer::getEnumCasts(), Address::getEnumCasts());
    }

    public static function data(): array
    {
        $addressRule = collect(CreateAddressRequest::data())->filter(function (RequestData $data) {
            return $data->getKey() !== 'customer_id';
        });

        return array_merge($addressRule->all(), CreateCustomerRequest::data());
    }

    public function authorize()
    {
        return true;
    }
}