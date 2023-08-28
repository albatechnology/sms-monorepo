<?php

namespace App\Models;

use App\Enums\NewTargetType;

class NewTarget extends BaseModel
{
    public $table = "new_targets";

    protected $guarded = [];

    protected $casts = [
        'type' => NewTargetType::class
    ];

    public function model()
    {
        return $this->morphTo('model');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'model_id', 'id')->where('model_type', 'user');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class, 'model_id', 'id')->where('model_type', 'channel');
    }

    // public function company()
    // {
    //     return $this->belongsTo(Company::class, 'model_id', 'id')->where('model_type', 'company');
    // }
}
