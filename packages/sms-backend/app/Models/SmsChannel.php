<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class SmsChannel extends BaseModel
{
    use SoftDeletes;
    public $table = 'sms_channels';
    protected $guarded = [];
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class, 'channel_id');
    }
}
