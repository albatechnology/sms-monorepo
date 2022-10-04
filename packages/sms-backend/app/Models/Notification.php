<?php

namespace App\Models;

use App\Enums\NotificationType;
use App\Invoker\IdeHelperMedia;
use App\Invoker\IdeHelperNotification;

/**
 * @mixin IdeHelperMedia
 * @mixin IdeHelperNotification
 */
class Notification extends BaseModel
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $dates = [
        'created_at',
        'updated_at',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'type' => NotificationType::class,
    ];
}