<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Interfaces\Tenanted;
use App\Traits\Auditable;
use App\Traits\CustomInteractsWithMedia;
use App\Traits\IsCompanyTenanted;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

class CustomerDeposit extends Model implements HasMedia, Tenanted
{
    use SoftDeletes, CustomInteractsWithMedia, IsCompanyTenanted, Auditable;

    public $table = 'customer_deposits';
    protected $guarded = [];

    protected $casts = [
        'customer_id' => 'integer',
        'user_id' => 'integer',
        'lead_id' => 'integer',
        'approved_by_id' => 'integer',
        'status' => PaymentStatus::class,
        'value' => 'integer',
    ];

    public function payment_type()
    {
        return $this->belongsTo(PaymentType::class, 'payment_type_id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
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
}
