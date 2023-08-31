<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Events\PaymentApproved;
use App\Events\PaymentDisapproved;
use App\Interfaces\Reportable;
use App\Interfaces\Tenanted;
use App\Traits\Auditable;
use App\Traits\IsSubscribedTenanted;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @mixin IdeHelperPayment
 */
class Payment extends BaseModel implements HasMedia, Tenanted, Reportable
{
    use SoftDeletes, InteractsWithMedia, Auditable, IsSubscribedTenanted;

    public const PROOF_COLLECTION = 'proof';

    const STATUS_SELECT = [
        'waiting'  => 'Waiting Approval',
        'rejected' => 'Rejected',
        'approved' => 'Approved',
    ];

    public $table = 'payments';

    protected $appends = [
        self::PROOF_COLLECTION,
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'subscribtion_user_id',
        'amount',
        'payment_type_id',
        'reference',
        'added_by_id',
        'approved_by_id',
        'status',
        'order_id',
        // 'company_id',
        // 'channel_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'amount'          => 'integer',
        'payment_type_id' => 'integer',
        'added_by_id'     => 'integer',
        'approved_by_id'  => 'integer',
        'order_id'        => 'integer',
        // 'channel_id'      => 'integer',
        'status'          => PaymentStatus::class,
    ];

    /**
     *  Setup model event hooks.
     */
    public static function boot()
    {
        self::creating(function (self $model) {
            if ($model->status->is(PaymentStatus::APPROVED)) {
                $model->approved_by_id = auth()->user()->id;
            }
        });

        self::updating(function (self $model) {
            if ($model->getOriginal('status') !== $model->status && $model->status->is(PaymentStatus::APPROVED)) {
                $model->approved_by_id = auth()->user()->id;
            }
        });

        self::created(function (self $model) {
            if ($model->status->is(PaymentStatus::APPROVED)) {
                PaymentApproved::dispatch($model);
            }
        });

        self::updated(function (self $model) {

            if ($model->propertyChanged('status') && $model->status->is(PaymentStatus::APPROVED)) {
                PaymentApproved::dispatch($model);
            }

            if ($model->propertyChanged('status') && $model->status->isNot(PaymentStatus::APPROVED)) {
                PaymentDisapproved::dispatch($model);
            }
        });

        parent::boot();
    }

    public function scopeWhereCreatedAtRange($query, $startDate = null, $endDate = null)
    {
        if (is_null($startDate)) $startDate = Carbon::now()->startOfMonth();
        if (is_null($endDate)) $endDate = Carbon::now()->endOfMonth();

        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereDate('orders.created_at', '>=', $startDate);
            $q->whereDate('orders.created_at', '<=', $endDate);
        });
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function subscribtionUser()
    {
        return $this->belongsTo(SubscribtionUser::class);
    }

    public function payment_type()
    {
        return $this->belongsTo(PaymentType::class, 'payment_type_id');
    }

    public function added_by()
    {
        return $this->belongsTo(User::class, 'added_by_id');
    }

    public function approved_by()
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function getProofAttribute()
    {
        $files = $this->getMedia('proof');
        $files->each(function ($item) {
            $item->url       = $item->getUrl();
            $item->thumbnail = $item->getUrl('thumb');
            $item->preview   = $item->getUrl('preview');
        });

        return $files;
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function scopeWherePaymentTypeId($query, ...$ids)
    {
        return $query->whereIn('payment_type_id', $ids);
    }

    public function scopeWhereAddedById($query, ...$ids)
    {
        return $query->whereIn('added_by_id', $ids);
    }

    public function scopeWhereApprovedById($query, ...$ids)
    {
        return $query->whereIn('approved_by_id', $ids);
    }

    public function scopeWhereOrderId($query, ...$ids)
    {
        return $query->whereIn('order_id', $ids);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    // public function getCompanyIdAttribute($value)
    // {
    //     return (int) $value;
    // }
}
