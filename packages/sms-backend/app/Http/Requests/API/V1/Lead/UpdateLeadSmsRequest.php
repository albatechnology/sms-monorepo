<?php

namespace App\Http\Requests\API\V1\Lead;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use App\Classes\DocGenerator\RequestData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdateLeadSmsRequest extends BaseApiRequest
{
    public static function data(): array
    {
        return [
            RequestData::make('label', Schema::TYPE_STRING, 'Lead label', 'required|string'),
            RequestData::make('name', Schema::TYPE_STRING, 'difa al maksud', 'nullable|string'),
            RequestData::make('email', Schema::TYPE_STRING, ApiDataExample::EMAIL)->uniqueRule('nullable|string|email', 'customers', 'email', 'customer')->ignoreRule(true),
            RequestData::make('phone', Schema::TYPE_STRING, ApiDataExample::PHONE)->uniqueRule('nullable|numeric', 'customers', 'phone')->ignoreRule(true),
            RequestData::make('address', Schema::TYPE_STRING, 'Desa Kaum Kebumen', 'nullable|string'),
            RequestData::make('note', Schema::TYPE_STRING, 'Lead note', 'nullable|string'),
            RequestData::make('product_brand_id', Schema::TYPE_INTEGER, 1, 'nullable|integer|exists:product_brands,id'),
            RequestData::make('voucher', Schema::TYPE_STRING, 'voucher', 'nullable|string'),
            RequestData::make('voucher_image', Schema::TYPE_STRING, 'voucher_image', 'nullable|image|mimes:jpg,jpeg,png,svg,webp'),
        ];
    }

    public function authorize()
    {
        return true;
    }
}
