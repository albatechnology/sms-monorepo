<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperChannelCategory
 */
class ChannelCategory extends BaseModel
{
    use SoftDeletes, Auditable;

    public $table = 'channel_categories';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'code',
        'slug',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     *  Setup model event hooks.
     */
    public static function boot()
    {
        self::created(function (self $model) {
            if(empty($model->code)){
                $model->update(["code" => sprintf("C%03d", $model->id)]);
            }
        });

        parent::boot();
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function channelCategoryChannels()
    {
        return $this->hasMany(Channel::class, 'channel_category_id', 'id');
    }
}
