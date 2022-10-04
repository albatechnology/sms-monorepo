<?php

namespace App\Models;

/**
 * @mixin IdeHelperTargetLine
 */
class TargetLine extends BaseModel
{

    public $table = 'target_lines';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'label',
        'model_type',
        'model_id',
        'target_id',
        'target',
        'value',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'target'     => 'integer',
        'value'      => 'integer',
        'model_id'   => 'integer',
        'target_id'  => 'integer',
    ];
}
