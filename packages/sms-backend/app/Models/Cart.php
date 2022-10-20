<?php

namespace App\Models;

use App\Classes\CartItem;
use App\Classes\CartItemAttribute;
use App\Classes\CartItemLine;
use App\Enums\DiscountError;
use App\Interfaces\Discountable;
use App\Traits\IsDiscountable;
use Illuminate\Support\Collection;

/**
 * @mixin IdeHelperCart
 */
class Cart extends BaseModel implements Discountable
{
    use IsDiscountable;

    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'items'          => CartItemAttribute::class,
        'user_id'        => 'integer',
        'customer_id'    => 'integer',
        'discount_id'    => 'integer',
        'total_discount' => 'integer',
        'total_price'    => 'integer',
        'discount_error' => DiscountError::class,
    ];

    /**
     *  Setup model event hooks.
     */
    public static function boot()
    {
        self::creating(function (self $model) {
            if (empty($model->items)) $model->items = new CartItem();
        });

        parent::boot();
    }

    public function getItemLinesAttribute()
    {
        return $this->items->cart_item_lines;
    }

    public function scopeWhereUser($query, $id)
    {
        return $query->where('user_id', $id);
    }

    public function user()
    {
        $this->belongsTo(User::class);
    }

    public function stockTransfers()
    {
        return $this->hasMany(StockTransfer::class);
    }

    public function getCustomerId(): int
    {
        return $this->customer_id;
    }

    public function addProduct(Product $unit, int $quantity = 1)
    {
        $this->resetDiscount();
        $this->items->addProductItem($unit, $quantity);
        $this->updatePricesFromItemLine();
    }

    /**
     * Setup for discountable
     * @return Collection
     */
    public function getDiscountableLines(): ?Collection
    {
        return $this->item_lines;
    }

    /**
     * Find product by id from items
     * @param int $id
     */
    public function getItem(int $id): ?CartItemLine
    {
        return $this->item_lines->first(function (CartItemLine $line) use ($id) {
            return $line->id == $id;
        });
    }
}
