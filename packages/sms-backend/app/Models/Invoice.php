<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @mixin IdeHelperInvoice
 */
class Invoice extends BaseModel implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, Auditable;

    public $table = 'invoices';

    protected $appends = [
        'files',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'order_id',
        'status',
        'fulfilled_by_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    const STATUS_SELECT = [
        'generating' => 'Generating',
        'send'       => 'Sent to Customer',
        'signed'     => 'Signed',
        'error'      => 'Error',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function getFilesAttribute()
    {
        return $this->getMedia('files');
    }

    public function fulfilled_by()
    {
        return $this->belongsTo(User::class, 'fulfilled_by_id');
    }
}
