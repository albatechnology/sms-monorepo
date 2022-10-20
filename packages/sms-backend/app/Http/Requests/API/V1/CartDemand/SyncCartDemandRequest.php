<?php

namespace App\Http\Requests\API\V1\CartDemand;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Models\CartDemand;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Illuminate\Validation\Rule;

class SyncCartDemandRequest extends BaseApiRequest
{
    protected ?string $model = CartDemand::class;

    public static function getSchemas(): array
    {
        return [
            Schema::array('items')->items(
                Schema::object()->properties(
                    Schema::string('name')->example('Lazy Girl')->description('Name of product'),
                    Schema::integer('price')->example(50000),
                    Schema::integer('quantity')->example(1),
                ),
            ),
            //Schema::integer('discount_id')->example(1),
            //Schema::integer('customer_id')->example(1),
        ];
    }

    protected static function data()
    {
    }

    public function toArray(): array
    {
        return [
            'items'            => 'required|array',
            'items.*.name'       => [
                'required',
            ],
            'items.*.price' => 'required|integer|min:1',
            'items.*.quantity' => 'required|integer|min:1',
            'discount_id'     => [
                'nullable', 'integer',
                Rule::exists('discounts', 'id')->where(function ($query) {
                    return $query->whereActive()->tenanted();
                }),
            ],
            'customer_id' => ['nullable', 'exists:customers,id']
        ];
    }

    public function authorize()
    {
        return true;
    }
}
