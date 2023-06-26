<?php

namespace App\Models;

// use App\Interfaces\Tenanted;
use App\Traits\CustomInteractsWithMedia;
use App\Traits\IsSubscribedTenanted;
use App\Traits\SaveToSubscriber;
// use App\Traits\IsCompanyTenanted;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

/**
 * @mixin IdeHelperPaymentCategory
 */
class PaymentCategory extends BaseModel implements HasMedia
{
    use SoftDeletes, CustomInteractsWithMedia, SaveToSubscriber, IsSubscribedTenanted;

    public $table = 'payment_categories';

    protected $appends = [
        'photo',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'subscribtion_user_id',
        'name',
        // 'company_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function paymentCategoryPaymentTypes()
    {
        return $this->hasMany(PaymentType::class, 'payment_category_id', 'id');
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
