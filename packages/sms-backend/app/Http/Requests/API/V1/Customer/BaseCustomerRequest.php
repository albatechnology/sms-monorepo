<?php

namespace App\Http\Requests\API\V1\Customer;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use App\Classes\DocGenerator\RequestData;
use App\Enums\PersonTitle;
use App\Models\Customer;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class BaseCustomerRequest extends BaseApiRequest
{
    protected ?string $model = Customer::class;

    public static function data(): array
    {
        return [
            RequestData::make('first_name', Schema::TYPE_STRING, 'Barrack', 'required|string|min:2|max:100'),
            RequestData::make('last_name', Schema::TYPE_STRING, 'Obama', 'nullable|string|min:2|max:100'),
            // RequestData::make('name', Schema::TYPE_STRING, 'Masfud Difa', 'nullable|string|min:2|max:100'),
            RequestData::make('date_of_birth', Schema::TYPE_STRING, ApiDataExample::TIMESTAMP, 'nullable|date|before:now'),
            RequestData::make('description', Schema::TYPE_STRING, 'First customer!', 'nullable|string|max:225'),
            RequestData::makeEnum('title', PersonTitle::class, true),
        ];
    }

    public function authorize()
    {
        return true;
    }
}
