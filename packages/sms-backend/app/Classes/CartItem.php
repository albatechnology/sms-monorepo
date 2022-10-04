<?php

namespace App\Classes;

use App\Http\Requests\API\V1\Cart\SyncCartRequest;
use App\Models\ProductUnit;
use Database\Factories\CartItemFactory;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use JsonSerializable;

class CartItem implements JsonSerializable, Arrayable
{
    use HasFactory;

    public Collection $cart_item_lines;

    public function __construct(array $attributes = [])
    {
        collect($attributes)->each(function ($data) {
            if (!$data instanceof CartItemLine) {
                throw new InvalidArgumentException('CartItem Instance must be initiated with CartItemLines');
            }
        });

        $this->cart_item_lines = collect($attributes) ?? collect([]);
    }

    public static function fromRequest(SyncCartRequest $request): static
    {
        $cartItem = new CartItem(collect($request->items)
            ->unique('id')
            ->map(fn ($data) => new CartItemLine($data))
            ->all());

        return $cartItem->fillItemLineData();
    }

    /**
     * Loop through each item lines and fill in the price and name
     * based on the product unit id.
     */
    public function fillItemLineData(): static
    {
        $product_unit_ids = $this->cart_item_lines->map(fn (CartItemLine $itemLine) => $itemLine->id);
        $product_units = ProductUnit::whereIn('id', $product_unit_ids)
            ->with(['product', 'colour', 'covering'])
            ->get()
            ->keyBy('id');

        $this->cart_item_lines = $this->cart_item_lines->map(function (CartItemLine $itemLine) use ($product_units) {
            $product_unit          = $product_units[$itemLine->id];
            $itemLine->name        = $product_unit->name;
            $itemLine->sku        = $product_unit->sku;
            $itemLine->unit_price  = $product_unit->price;
            $itemLine->total_price = $product_unit->price * $itemLine->quantity;
            $itemLine->colour      = $product_unit->colour;
            $itemLine->covering    = $product_unit->covering;
            return $itemLine;
        });

        return $this;
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return CartItemFactory
     */
    protected static function newFactory()
    {
        return new CartItemFactory();
    }

    public function addProductUnitItem(ProductUnit $unit, int $quantity): void
    {
        $this->cart_item_lines = $this->cart_item_lines->push(CartItemLine::fromProductUnit($unit, $quantity));
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray()
    {
        return collect($this->cart_item_lines)->map(fn (CartItemLine $line) => $line->toArray())->toArray();
    }
}
