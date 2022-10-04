<?php

namespace App\Models;

use App\Enums\CacheTags;
use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * @mixin IdeHelperSupervisorType
 */
class SupervisorType extends BaseModel
{
    use SoftDeletes, Auditable, HasSlug;

    public $table = 'supervisor_types';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'level',
        'can_assign_lead',
        'discount_approval_limit_percentage',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'can_assign_lead'                    => 'bool',
        'level'                              => 'integer',
        'discount_approval_limit_percentage' => 'integer',
    ];



    /**
     *  Setup model event hooks.
     */
    public static function boot()
    {
        self::saved(function (self $model) {
            cache_service()->forget([CacheTags::SUPERVISOR_TYPE]);
        });

        self::deleted(function (self $model) {
            cache_service()->forget([CacheTags::SUPERVISOR_TYPE]);
        });

        parent::boot();
    }

    public function supervisorTypeUsers()
    {
        return $this->hasMany(User::class, 'supervisor_type_id', 'id');
    }

    public function supervisorDiscountApprovalLimits()
    {
        return $this->hasMany(SupervisorDiscountApprovalLimit::class, 'supervisor_type_id', 'id');
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['name'])
            ->saveSlugsTo('code');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
