<?php

namespace App\Models;

use App\Classes\CartItemLine;
use App\Enums\DiscountError;
use App\Interfaces\Discountable;
use App\Traits\CustomInteractsWithMedia;
use App\Traits\IsDiscountable;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;

/**
 * @mixin IdeHelperCart
 */
class CartDemand extends BaseModel implements Discountable, HasMedia
{
    use IsDiscountable, CustomInteractsWithMedia;
    public $table = 'cart_demands';

    protected $guarded = [];
    protected $appends = [
        'photo',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'items'          => 'array',
        'user_id'        => 'integer',
        'order_id'       => 'integer',
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
            if (empty($model->items)) $model->items = [];
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

    public function scopeWhereOrdered($query)
    {
        return $query->whereNotNull('order_id');
    }

    public function scopeWhereNotOrdered($query)
    {
        return $query->whereNull('order_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPhotoAttribute()
    {
        $files = $this->getMedia('photo');
        $files->each(function ($item) {
            $item->url       = $item->getUrl();
            $item->thumbnail = $item->getUrl('thumb');
            $item->preview   = $item->getUrl('preview');
        });

        return $files;
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
