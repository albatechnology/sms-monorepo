<?php

namespace App\Models;

use App\Traits\Auditable;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperBanner
 */
class Banner extends BaseModel
{
    use SoftDeletes, Auditable;

    public $table = 'banners';

    protected $dates = [
        'start_time',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'bannerable_type',
        'bannerable',
        'is_active',
        'start_time',
        'end_time',
        'company_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getStartTimeAttribute($value)
    {
        if(!$value) return null;

        $value = Carbon::createFromFormat('Y-m-d H:i:s', $value);
        return is_api() ? $value->toISOString() : $value->format(config('panel.date_format') . ' ' . config('panel.time_format'));
    }

    public function setStartTimeAttribute($value)
    {
        $this->attributes['start_time'] = $value ? Carbon::parse($value) : null;
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
