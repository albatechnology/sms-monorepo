<?php

namespace App\Models;

use App\Enums\TargetType;

/**
 * @mixin IdeHelperTargetTypePriority
 */
class TargetTypePriority extends BaseModel
{
    protected $casts = [
        'target_type' => TargetType::class,
        'priority'    => 'integer',
    ];


}