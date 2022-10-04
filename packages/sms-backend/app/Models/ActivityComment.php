<?php

namespace App\Models;

use App\Interfaces\Tenanted;
use App\Traits\Auditable;
use App\Traits\IsTenanted;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperActivityComment
 */
class ActivityComment extends BaseModel implements Tenanted
{
    use SoftDeletes, Auditable, IsTenanted;

    public $table = 'activity_comments';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'content',
        'user_id',
        'activity_id',
        'activity_comment_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'user_id'             => 'integer',
        'activity_id'         => 'integer',
        'activity_comment_id' => 'integer',
    ];

    /**
     *  Setup model event hooks.
     */
    public static function boot()
    {
        self::created(function (self $model) {
            $model->activity->refreshCommentStats();

            // send notification to user and his bum
            $type = \App\Enums\NotificationType::ActivityReminder();
            $link = sprintf(config("notification-link.{$type->key}"), $model->activity_id);

            $sender = $model->user;
            $recipient = $model->activity->user;
            $customer = $model->activity->customer->name;
            $bum = $recipient->getSalesSupervisor();
            if (isset($recipient->notificationDevices) && count($recipient->notificationDevices) > 0) {
                \App\Events\SendExpoNotification::dispatch([
                    'receipents' => $recipient,
                    'badge_for' => $recipient,
                    'title' => $sender->name . " comments on your activity",
                    'body' => $sender->name .  " comments on customer activity on behalf of " . $customer,
                    'code' => $type->key,
                    'link' => $link,
                ]);
            }
            if (isset($bum->notificationDevices) && count($bum->notificationDevices) > 0) {
                \App\Events\SendExpoNotification::dispatch([
                    'receipents' => $bum,
                    'badge_for' => $bum,
                    'title' => $sender->name . " comments on " . $recipient->name . "'s activity",
                    'body' => $sender->name .  " comments on " . $recipient->name . "'s activity on behalf of " . $customer,
                    'code' => $type->key,
                    'link' => $link,
                ]);
            }
        });

        parent::boot();
    }

    public function activityCommentActivityComments()
    {
        return $this->hasMany(ActivityComment::class, 'activity_comment_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    public function activity_comment()
    {
        return $this->belongsTo(ActivityComment::class, 'activity_comment_id');
    }

    public function activityComment()
    {
        return $this->activity_comment();
    }

    public function userCanAccess(User $user = null): bool
    {
        if (!$user) $user = tenancy()->getUser();
        if ($user->is_admin) return true;
        if ($user->is_sales) return $this->user_id === $user->id;
        if ($user->is_supervisor) return $user->descendants->pluck('id')->contains($this->user_id);
        return false;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
