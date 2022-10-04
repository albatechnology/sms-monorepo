<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperChannelUser
 */
class ChannelUser extends Pivot
{
    protected $table = 'channel_user';
}
