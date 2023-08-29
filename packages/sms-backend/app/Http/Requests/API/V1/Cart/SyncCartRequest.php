<?php

namespace App\Http\Requests\API\V1\Cart;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Models\Cart;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Illuminate\Validation\Rule;

class SyncCartRequest extends BaseApiRequest
{
    protected ?string $model = Cart::class;

    public static function getSchemas(): array
    {
        return [
            Schema::array('items')->items(
                Schema::object()->properties(
                    Schema::integer('id')->example(1)->description('The product id to add to cart'),
                    // Schema::string('sku')->example('1441068311742')->description('sku of product unit'),
                    Schema::integer('quantity')->example(1)
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
            'items.*.id'       => [
                'required',
                Rule::exists('products', 'id')->where(function ($query) {
                    // return $query->tenanted();
                    return $query->query();
                }),
            ],
            'items.*.quantity' => 'required|integer|min:1',
            // 'sku' => ['nullable', 'exists:products,sku'],
            'discount_id'     => [
                'nullable', 'integer',
                Rule::exists('discounts', 'id')->where(function ($query) {
                    // return $query->whereActive()->tenanted();
                    return $query->whereActive();
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
