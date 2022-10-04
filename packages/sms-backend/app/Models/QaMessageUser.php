<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Pivot for messages that is still unread for any given users.
 * Once the user has read the message, the record should be deleted.
 *
 * Class QaMessageUser
 * @package App\Models
 * @mixin IdeHelperQaMessageUser
 */
class QaMessageUser extends Pivot
{
    protected $casts = [
        'user_id'    => 'integer',
        'message_id' => 'integer',
        'topic_id'   => 'integer',
    ];

    /**
     *  Setup model event hooks.
     */
    public static function boot()
    {
        self::created(function (self $model) {
            // TODO: send notification of this unread message to user
        });

        parent::boot();
    }

    /**
     * All unread messages count for the logged in user.
     * @return int
     */
    public static function unreadCount()
    {
        return self::where('user_id', user()->id)->count();
    }
}