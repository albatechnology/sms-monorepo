<?php

namespace App\Models;

use App\Services\ReportService;

/**
 * Everytime a new model is created that would contribute
 * to one or many target, TargetMap record will be created
 * to connect relationship from the model (e.g., order) to the target.
 *
 * Class TargetMap
 * @package App\Models
 * @mixin IdeHelperTargetMap
 */
class TargetMap extends BaseModel
{
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'model_type',
        'model_id',
        'target_id',
        'value',
        'context',
    ];

    protected $casts = [
        'value'    => 'integer',
        'model_id' => 'integer',
        'context'  => 'json'
    ];

    /**
     *  Setup model event hooks.
     */
    public static function boot()
    {
        self::created(function (self $model) {
            app(ReportService::class)->newTargetMapAdded($model);
        });

        parent::boot();
    }

    public function model()
    {
        return $this->morphTo('model');
    }

    public function target()
    {
        return $this->belongsTo(Target::class);
    }
}