<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Many to Many pivot, determines the users subscription
 * to any given qa topic.
 *
 * Class QaTopicUser
 * @package App\Models
 * @mixin IdeHelperQaTopicUser
 */
class QaTopicUser extends Pivot
{
    protected $casts = [
        'user_id'  => 'integer',
        'topic_id' => 'integer',
    ];
}