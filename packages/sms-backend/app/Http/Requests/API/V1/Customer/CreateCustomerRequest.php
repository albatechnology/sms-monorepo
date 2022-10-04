<?php

namespace App\Http\Requests\API\V1\Customer;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use App\Classes\DocGenerator\RequestData;
use App\Models\Customer;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class CreateCustomerRequest extends BaseApiRequest
{
    protected ?string $model = Customer::class;

    public static function data(): array
    {
        return [
            ...BaseCustomerRequest::data(),
            RequestData::make('email', Schema::TYPE_STRING, ApiDataExample::EMAIL, 'required|string|email|unique:customers'),
            RequestData::make('phone', Schema::TYPE_STRING, '081837273726', 'required|numeric|unique:customers'),
        ];
    }

    public function authorize()
    {
        return true;
    }
}
