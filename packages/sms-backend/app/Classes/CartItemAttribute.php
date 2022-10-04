<?php

namespace App\Classes;

use Database\Factories\CartItemFactory;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use JsonSerializable;

class CartItemAttribute implements CastsAttributes
{

    /**
     * Cast the given value.
     *
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return CartItem
     */
    public function get($model, $key, $value, $attributes)
    {
        $data = json_decode($value, true);

        $item_lines = collect($data)->map(function ($data) {
            return new CartItemLine($data);
        });

        return new CartItem($item_lines->all());
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return mixed
     */
    public function set($model, $key, $value, $attributes)
    {
        if (!$value instanceof CartItem) {
            throw new InvalidArgumentException('The given value is not an CartItem instance.');
        }

        return json_encode($value);
    }
}
