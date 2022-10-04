<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperQaMessage
 */
class QaMessage extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'topic_id',
        'sender_id',
        'content',
    ];

    protected $casts = [
        'topic_id'  => 'integer',
        'sender_id' => 'integer',
    ];

    protected $dates = [
        'sent_at',
    ];

    /**
     *  Setup model event hooks.
     */
    public static function boot()
    {
        self::created(function (self $model) {
            $model->createUnread();
            $model->updateTopicLatestMessage();
        });

        parent::boot();
    }

    public function topic()
    {
        return $this->belongsTo(QaTopic::class);
    }

    public function sender()
    {
        return $this->hasOne(User::class, 'id', 'sender_id')->withTrashed();
    }

    /**
     * scope by messages that has not been read by the
     * authenticated user
     *
     * @param $query
     * @param $is_unread
     * @return mixed
     */
    public function scopeIsUnread(Builder $query, $is_unread)
    {
        if ($is_unread) {
            return $query->whereHas('unreads', function ($query) {
                $query->where('user_id', user()->id);
            });
        } else {
            return $query->whereDoesntHave('unreads', function ($query) {
                $query->where('user_id', user()->id);
            });
        }
    }

    /**
     * create unread row to everyone subscribed to this message topic
     * except the sender
     */
    protected function createUnread()
    {
        $subsribers = QaTopicUser::where('topic_id', $this->topic_id)->get();

        $subsribers->each(function (QaTopicUser $subscriber) {
            if ($subscriber->user_id !== $this->sender_id) {
                QaMessageUser::create(
                    [
                        'topic_id'   => $this->topic_id,
                        'message_id' => $this->id,
                        'user_id'    => $subscriber->topic_id,
                    ]
                );
            }
        });
    }

    public function unreads()
    {
        return $this->hasMany(QaMessageUser::class, 'message_id');
    }

    /**
     * Set this message as the latest message of the topic
     * @return bool
     */
    public function updateTopicLatestMessage()
    {
        return $this->topic->update(['latest_message_id' => $this->id]);
    }
}
