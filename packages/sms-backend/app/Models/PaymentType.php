<?php

namespace App\Models;

// use App\Interfaces\Tenanted;
use App\Traits\Auditable;
use App\Traits\CustomInteractsWithMedia;
use App\Traits\IsSubscribedTenanted;
use App\Traits\SaveToSubscriber;
// use App\Traits\IsCompanyTenanted;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

/**
 * @mixin IdeHelperPaymentType
 */
class PaymentType extends BaseModel implements HasMedia
{
    use SoftDeletes, Auditable, CustomInteractsWithMedia, SaveToSubscriber, IsSubscribedTenanted;

    public $table = 'payment_types';

    protected $appends = [
        'photo',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $guarded = [];

    protected $casts = [
        'payment_category_id' => 'integer',
        // 'company_id'          => 'integer',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    // region Relationship
    public function paymentTypePayments()
    {
        return $this->hasMany(Payment::class, 'payment_type_id', 'id');
    }

    public function payment_category()
    {
        return $this->belongsTo(PaymentCategory::class, 'payment_category_id');
    }

    // public function company()
    // {
    //     return $this->belongsTo(Company::class, 'company_id');
    // }

    //endregion

    public function scopeWherePaymentCategoryId($query, ...$ids)
    {
        return $query->whereIn('payment_category_id', $ids);
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
}
