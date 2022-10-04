<?php

namespace App\Models;

use App\Enums\AddressType;
use App\Exceptions\InvalidLastCustomerAddressDeletionException;
use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperAddress
 */
class Address extends BaseModel
{
    use SoftDeletes, Auditable;

    public $table = 'addresses';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'type' => AddressType::class,
    ];


    /**
     *  Setup model event hooks.
     */
    public static function boot()
    {
        self::deleting(function (self $model) {
            // prevent deletion if this is the only customer address left
            $customerAddressCount = Address::where('customer_id', $model->customer_id)->count();
            if ($customerAddressCount == 1) {
                throw new InvalidLastCustomerAddressDeletionException();
            }
        });

        self::deleted(function (self $model) {

            // if this address is used as customer's default address
            // we remove them from being user's default
            if ($model->customer->default_address_id == $model->id) {
                $model->customer->default_address_id = null;
                $model->customer->save();
            }
        });

        parent::boot();
    }

    public function addressOrders()
    {
        return $this->hasMany(Order::class, 'address_id', 'id');
    }

    public function addressTaxInvoices()
    {
        return $this->hasMany(TaxInvoice::class, 'address_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function toString(): string
    {
        $data = [
            $this->address_line_1,
            $this->address_line_2,
            $this->address_line_3,
            $this->city,
            $this->province,
            $this->postcode,
            $this->country,
            $this->phone,
        ];

        $notEmpty = collect($data)->filter(fn($q) => !empty($q));

        return $notEmpty->implode(', ');
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
}
