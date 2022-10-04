<?php

namespace App\Models;

use App\Interfaces\Tenanted;
use App\Traits\CustomInteractsWithMedia;
use App\Traits\IsTenanted;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

/**
 * @mixin IdeHelperQaTopic
 */
class QaTopic extends BaseModel implements Tenanted, HasMedia
{
    use IsTenanted, SoftDeletes, CustomInteractsWithMedia;

    protected $fillable = [
        'subject',
        'creator_id',
        'sent_at',
    ];

    protected $casts = [
        'creator_id' => 'integer',
    ];


    public function hasUnread(): bool
    {
        return QaMessageUser::where('user_id', user())->where('topic_id', $this->id)->count() > 0;
    }

    public function messages(): HasMany
    {
        return $this->hasMany(QaMessage::class, 'topic_id');
        //->orderBy('created_at', 'desc');
    }

    public function latestMessage()
    {
        return $this->belongsTo(QaMessage::class, 'latest_message_id');
    }

    public function isInvolved(?User $user = null): bool
    {
        if (empty($user)) $user = tenancy()->getUser();
        return QaTopicUser::where('user_id', $user->id)->where('topic_id', $this->id)->count() > 0;
    }

    public function scopeInvolved($query)
    {
        return $query->whereHas('subscribers', function ($query) {
            $query->where('user_id', user()->id);
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function subscribers()
    {
        return $this->hasMany(QaTopicUser::class, 'topic_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'qa_topic_user', 'topic_id', 'user_id');
    }

    public function getApiImagesAttribute()
    {
        return $this->images;
    }
}
