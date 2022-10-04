<?php

namespace App\Http\Requests\API\V1\CustomerDeposit;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use App\Classes\DocGenerator\RequestData;
use App\Models\CustomerDeposit;
use App\Models\ProductBrand;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class CreateCustomerDepositRequest extends BaseApiRequest
{
    protected ?string $model = CustomerDeposit::class;

    public static function data(): array
    {
        return [
            RequestData::make('customer_id', Schema::TYPE_INTEGER, 1, 'required|exists:customers,id'),
            // RequestData::make('user_id', Schema::TYPE_INTEGER, 1, 'required|exists:users,id'),
            RequestData::make('lead_id', Schema::TYPE_INTEGER, 1, 'required|exists:leads,id'),
            RequestData::make('product_brand', Schema::TYPE_STRING, 'Product brand', 'nullable|string|min:1|max:200'),
            RequestData::make('product_unit', Schema::TYPE_STRING, 'Product unit', 'nullable|string|min:1|max:200'),
            RequestData::make('value', Schema::TYPE_INTEGER, 1000, 'required|min:0'),
            RequestData::make('description', Schema::TYPE_STRING, 'Description', 'required|string|min:1|max:200'),
        ];
    }

    public function authorize()
    {
        return true;
    }
}
