<?php

namespace App\Http\Requests\API\V1;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\RequestData;
use App\Enums\AddressType;
use App\Models\Address;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdateAddressRequest extends BaseApiRequest
{
    protected ?string $model = Address::class;

    public static function data(): array
    {
        return [
            RequestData::make('address_line_1', Schema::TYPE_STRING, '1706 Wilkinson Locks Apt. 161', 'required|string|min:5'),
            RequestData::make('address_line_2', Schema::TYPE_STRING, '7500 Thompson Mills Suite 103', 'nullable|string|min:5'),
            RequestData::make('address_line_3', Schema::TYPE_STRING, 'test line 3', 'nullable|string|min:5'),
            RequestData::make('postcode', Schema::TYPE_STRING, '12332', 'nullable|string|min:2|max:10'),
            RequestData::make('city', Schema::TYPE_STRING, 'Jakarta', 'nullable|string|min:2'),
            RequestData::make('country', Schema::TYPE_STRING, 'indonesia', 'nullable|string|min:2'),
            RequestData::make('province', Schema::TYPE_STRING, 'Jawa Barat', 'nullable|string|min:2'),
            RequestData::make('phone', Schema::TYPE_STRING, '(751) 204-1106', 'nullable|string|min:5'),
            RequestData::makeEnum('type', AddressType::class, true),
            RequestData::make('customer_id', Schema::TYPE_INTEGER, 1, 'required|exists:customers,id'),
        ];
    }

    public function authorize()
    {
        return true;
    }
}