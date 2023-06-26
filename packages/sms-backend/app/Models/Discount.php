<?php

namespace App\Models;

use App\Enums\ProductCategory;
use App\Enums\DiscountScope;
use App\Enums\DiscountType;
// use App\Interfaces\Tenanted;
use App\Traits\Auditable;
use App\Traits\IsSubscribedTenanted;
use App\Traits\SaveToSubscriber;
// use App\Traits\HasProductList;
// use App\Traits\IsCompanyTenanted;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperDiscount
 */
class Discount extends BaseModel
{
    use SoftDeletes, Auditable, SaveToSubscriber, IsSubscribedTenanted;

    public $table = 'discounts';

    protected $guarded = ['id'];

    protected $dates = [
        'start_time',
        'end_time',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'value'                        => 'float',
        'max_discount_price_per_order' => 'integer',
        'max_use_per_customer'         => 'integer',
        'min_order_price'              => 'integer',
        // 'company_id'                   => 'integer',
        'promo_id'                     => 'integer',
        'type'                         => DiscountType::class,
        'scope'                        => DiscountScope::class,
        'product_category'        => ProductCategory::class,
        'product_ids'             => 'array',
    ];

    /**
     * Formatted string  of discount value based on type
     * @param $value
     * @return string
     */
    public function getValueFormattedAttribute()
    {
        if ($this->type->is(DiscountType::NOMINAL)) {
            return rupiah($this->value);
        }

        if ($this->type->is(DiscountType::PERCENTAGE)) {
            return $this->value . '%';
        }

        return $this->value;
    }

    public function getStartTimeAttribute($value)
    {
        if (!$value) return null;

        $value = Carbon::createFromFormat('Y-m-d H:i:s', $value);
        return is_api() ? $value->toISOString() : $value->format(config('panel.date_format') . ' ' . config('panel.time_format'));
    }

    public function setStartTimeAttribute($value)
    {
        $this->attributes['start_time'] = $value ? Carbon::parse($value) : null;
    }

    public function getEndTimeAttribute($value)
    {
        if (!$value) return null;

        $value = Carbon::createFromFormat('Y-m-d H:i:s', $value);
        return is_api() ? $value->toISOString() : $value->format(config('panel.date_format') . ' ' . config('panel.time_format'));
    }

    public function setEndTimeAttribute($value)
    {
        $this->attributes['end_time'] = $value ? Carbon::parse($value) : null;
    }

    // public function company()
    // {
    //     return $this->belongsTo(Company::class, 'company_id');
    // }

    public function promo()
    {
        return $this->belongsTo(Promo::class, 'promo_id');
    }

    public function customerDiscountUse()
    {
        return $this->hasMany(CustomerDiscountUse::class);
    }

    public function customerReachUseLimit(int $customer_id): bool
    {
        if (!$this->max_use_per_customer) return false;

        return $this->customerUseCount($customer_id) >= $this->max_use_per_customer;
    }

    public function customerUseCount(int $customer_id): int
    {
        $use = CustomerDiscountUse::where('discount_id', $this->id)
            ->where('customer_id', $customer_id)
            ->first();

        if (!$use) return 0;

        return $use->use_count ?? 0;
    }

    public function scopeCustomSearch($query, $key)
    {
        return $query->where('name', 'LIKE', "%" . $key . "%")->orWhere('name', 'LIKE', "%" . $key . "%");
    }

    public function scopeWhereNameLike($query, $key)
    {
        return $query->orWhere('name', 'LIKE', "%" . $key . "%");
    }

    public function scopeWhereActivationCodeLike($query, $key)
    {
        return $query->orWhere('activation_code', 'LIKE', "%" . $key . "%");
    }

    public function scopeWhereIsActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeWhereActive($query, $code = null)
    {
        // $query = $query->where('is_active', 1)
        //     ->where('start_time', '<', now())
        //     ->where(function ($q) {
        //         $q->where('end_time', '>', now())
        //             ->orWhere('end_time', null);
        //     });
        $query = $query->where('is_active', 1)
            ->whereHas('promo', fn ($q) => $q->whereActive());

        $query = $code ? $query->where('activation_code', $code) : $query->whereNull('activation_code');
        // if ($code) $query = $query->where('name', 'like', '%' . $code . '%');

        return $query;
    }

    public function isActiveNow(): bool
    {
        if (!$this->is_active) return false;
        if (now()->isBefore($this->promo->start_time)) return false;
        if (!empty($this->promo->end_time) && now()->isAfter($this->promo->end_time)) return false;

        return true;
    }

    /**
     * Does this discount apply to the
     * @return bool
     */
    public function applyToProduct(): bool
    {
        return !$this->applyToOrder();
    }

    /**
     * Does this discount apply to the order via
     * TRANSACTION discount scope
     * @return bool
     */
    public function applyToOrder(): bool
    {
        return $this->scope->applyToOrder();
    }

    /**
     * Get the properties for record purposes
     */
    public function toRecord()
    {
        $data = $this->toArray();
        unset($data['created_at'], $data['updated_at'], $data['deleted_at']);

        return $data;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function productBrand()
    {
        return $this->belongsTo(ProductBrand::class, 'product_brand_id');
    }
}
