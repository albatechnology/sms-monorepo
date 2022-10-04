<?php

namespace App\Http\Requests\API\V1\Customer;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use App\Classes\DocGenerator\RequestData;
use App\Models\Customer;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends BaseApiRequest
{
    protected ?string $model = Customer::class;

    public static function data(): array
    {
        return [

            ...BaseCustomerRequest::data(),

            RequestData::make('email', Schema::TYPE_STRING, ApiDataExample::EMAIL)
                ->uniqueRule('required|string|email', 'customers', 'email', 'customer'),

            RequestData::make('phone', Schema::TYPE_STRING, '081837273726')
                ->uniqueRule('required|numeric', 'customers', 'phone', 'customer'),

            RequestData::make(
                'default_address_id',
                Schema::TYPE_INTEGER,
                1,
                fn (self $request) => [
                    'nullable',
                    Rule::exists('addresses', 'id')
                        ->where('customer_id', $request->route('customer')->id),
                ],
                'nullable|exists:addresses'
            ),
        ];
    }

    public function authorize()
    {
        return true;
    }
}
