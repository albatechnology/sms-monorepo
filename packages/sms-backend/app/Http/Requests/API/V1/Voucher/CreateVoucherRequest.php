<?php

namespace App\Http\Requests\API\V1\Voucher;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use App\Classes\DocGenerator\RequestData;
use App\Models\Voucher;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class CreateVoucherRequest extends BaseApiRequest
{
    protected ?string $model = Voucher::class;

    public static function getSchemas(): array
    {
        return [
            Schema::integer('customer_id')->example(1)->nullable(),
            Schema::array('vouchers')->items(
                Schema::object()->properties(
                    Schema::string('id')->example("GratisOngkir")->description('Voucher id'),
                    Schema::integer('value')->example(1000000)->description('Voucher value in nominal'),
                ),
            ),
        ];
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'nullable|exists:customers,id',
            'vouchers' => 'required|array',
            'vouchers.*.id' => 'required|string',
            'vouchers.*.value' => 'required|integer',
        ];
    }

    protected static function data()
    {
        return [];
    }

    public function authorize()
    {
        return true;
    }
}
