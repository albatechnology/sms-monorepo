<?php

namespace App\Models;

use App\Enums\PersonTitle;
use App\Traits\Auditable;
use App\Traits\IsSubscribedTenanted;
use App\Traits\SaveToSubscriber;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperCustomer
 */
class Customer extends BaseModel
{
    use SoftDeletes, Auditable, SaveToSubscriber, IsSubscribedTenanted;

    const TITLE_SELECT = [
        'Mr.'  => 'Mr.',
        'Mrs.' => 'Mrs.',
        'Ms.'  => 'Ms.',
        'PT.'  => 'PT.',
    ];

    public $table = 'customers';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'subscribtion_user_id',
        'title',
        // 'name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'description',
        'default_address_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'source',
        'user_sms_id',
    ];

    protected $casts = [
        'has_activity'       => 'bool',
        'default_address_id' => 'integer',
        'date_of_birth'      => 'date',
        'title'              => PersonTitle::class,
    ];

    public function customerLeads()
    {
        return $this->hasMany(Lead::class, 'customer_id', 'id');
    }

    public function customerActivity()
    {
        return $this->hasMany(Activity::class, 'customer_id', 'id');
    }

    public function customerAddresses()
    {
        return $this->hasMany(Address::class, 'customer_id', 'id');
    }

    public function defaultCustomerAddress()
    {
        return $this->hasOne(Address::class, 'id', 'default_address_id');
    }

    public function customerTaxInvoices()
    {
        return $this->hasMany(TaxInvoice::class, 'customer_id', 'id');
    }

    public function subscribtionUser()
    {
        return $this->belongsTo(SubscribtionUser::class);
    }

    /**
     * @param $query
     * @param string $ids ids, comma separated
     * @return mixed
     */
    public function scopeIds($query, ...$ids): mixed
    {
        return $query->whereIn('id', $ids);
    }

    public function scopeWhereNameLike($query, $name)
    {
        return $query->where('first_name', 'LIKE', "%$name%")
            ->orWhere('last_name', 'LIKE', "%$name%");
    }

    public function scopeWhereSearch($query, $key)
    {
        return $query->where('first_name', 'LIKE', "%$key%")
            ->orWhere('last_name', 'LIKE', "%$key%")
            ->orWhere('email', 'LIKE', "%$key%")
            ->orWhere('phone', 'LIKE', "%$key%");
    }

    public function getFullNameAttribute()
    {
        return implode(' ', [$this->first_name, $this->last_name]);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function evaluateHasActivity(): void
    {
        if ($this->has_activity) {
            return;
        }

        if (!$this->customerActivity()->exists()) {
            return;
        }

        $this->has_activity = true;
        $this->save();
    }

    public function scopeWhereCreatedAtRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereDate('customers.created_at', '>=', $startDate);
            $q->whereDate('customers.created_at', '<=', $endDate);
        });
    }
}
