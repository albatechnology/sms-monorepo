<?php

namespace App\Classes;

use App\Enums\DiscountError;
use App\Interfaces\DiscountableLine;
// use App\Models\Colour;
// use App\Models\Covering;
use App\Models\Discount;
use App\Models\Product;
use App\Traits\IsDiscountable;
use Database\Factories\CartItemLineFactory;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use JsonSerializable;

class CartItemLine implements JsonSerializable, Arrayable, DiscountableLine
{
    use HasFactory, IsDiscountable;

    public int     $id;
    public int     $quantity;
    // public string $sku;
    public ?int    $product_id;
    public ?int    $discount_id;
    public ?string $name;

    public int $unit_price     = 0;
    public int $total_price    = 0;
    public int $total_discount = 0;

    public ?Discount      $discount            = null;
    public ?DiscountError $discount_error      = null;

    // record properties
    // public ?Colour   $colour;
    // public ?Covering $covering;

    public function __construct($attributes)
    {
        $this->id             = $attributes['id'];
        $this->quantity       = $attributes['quantity'];
        // // $this->sku       = $attributes['sku'];
        $this->product_id     = $attributes['product_id'] ?? null;
        $this->discount_id    = $attributes['discount_id'] ?? null;
        $this->name           = $attributes['name'] ?? null;
        $this->unit_price     = $attributes['unit_price'] ?? 0;
        $this->total_discount = $attributes['total_discount'] ?? 0;
        $this->total_price    = $attributes['total_price'] ?? 0;
        // $this->colour         = isset($attributes['colour']) ? (new Colour())->forceFill($attributes['colour']) : null;
        // $this->covering       = isset($attributes['covering']) ? (new Covering())->forceFill($attributes['covering']) : null;
    }

    public static function fromProduct(Product $unit, int $quantity): self
    {
        return new self(
            [
                'id'          => $unit->id,
                'quantity'    => $quantity,
                // // 'sku'    => $unit->sku,
                'product_id'  => $unit->product_id,
                'name'        => $unit->name,
                'unit_price'  => $unit->price,
                'total_price' => $unit->price,
                // 'colour'      => $unit->colour->toArray(),
                // 'covering'    => $unit->covering->toArray(),
            ]
        );
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return CartItemLineFactory
     */
    protected static function newFactory()
    {
        return new CartItemLineFactory();
    }

    /**
     * Given the product unit id, we fetch its name
     * and price if not already loaded
     */
    public function fillData()
    {
        $unit              = Product::findOrFail($this->id);
        $this->name        = $unit->name;
        // // $this->sku        = $unit->sku;
        $this->unit_price  = $unit->unit_price;
        $this->total_price = ($unit->price * $this->quantity) - $this->total_discount;
        $this->product_id  = $unit->product_id;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'id'             => $this->id,
            'quantity'       => $this->quantity,
            // // 'sku'             => $this->sku,
            'product_id'     => $this->product_id,
            'discount_id'    => $this->discount_id,
            'name'           => $this->name,
            'unit_price'     => $this->unit_price,
            'total_discount' => $this->total_discount,
            'total_price'    => $this->total_price,
            // 'colour'         => $this->colour?->toRecord() ?? null,
            // 'covering'       => $this->covering?->toRecord() ?? null,
        ];
    }

    /**
     * Cast the given value.
     *
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return self
     */
    public function get($model, $key, $value, $attributes): CartItemLine
    {
        $data = json_decode($value, true);
        return new self($data);
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
        if (!$value instanceof self) {
            throw new InvalidArgumentException('The given value is not an CartItemLine instance.');
        }

        return json_encode($value);
    }


    /**
     * get product id if applicable
     */
    public function getProductId(): int
    {
        return $this->product_id;
    }

    /**
     * part of interface but not relevant for cart.
     * @param int $price
     */
    public function setTotalCascadedDiscount(int $price)
    {
        //$this->total_cascaded_discount = $price;
    }
}
