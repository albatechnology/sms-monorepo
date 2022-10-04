<?php

namespace App\Http\Requests\API\V1\Lead;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use App\Classes\DocGenerator\RequestData;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class CreateLeadSmsRequest extends BaseApiRequest
{
    public static function data(): array
    {
        return [
            RequestData::make('label', Schema::TYPE_STRING, 'Lead label', 'required|string'),
            RequestData::make('name', Schema::TYPE_STRING, 'difa al maksud', 'required|string'),
            RequestData::make('email', Schema::TYPE_STRING, ApiDataExample::EMAIL, 'required|email'),
            RequestData::make('phone', Schema::TYPE_STRING, '085691977176', 'required|numeric'),
            RequestData::make('address', Schema::TYPE_STRING, 'Desa Kaum Kebumen', 'required|string'),
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

    // public function rules(): array
    // {
    //     return [
    //         'label' => 'required|string',
    //         'name' => 'required|string',
    //         'email' => 'required|email|exists:customers,email',
    //         'phone' => 'required|numeric|exists:customers,phone',
    //         'address' => 'required|string',
    //         'note' => 'nullable|string',
    //         'product_brand_id' => 'nullable|integer|exists:product_brands,id',
    //         'voucher' => 'nullable|string',
    //         'voucher_image' => 'nullable|image|mimes:jpg,jpeg,png,svg,webp',
    //     ];
    // }
}
