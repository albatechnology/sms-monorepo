<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscribtionPackage extends Model
{
    protected $guarded = [];

    protected $casts = [
        'can_discount' => 'boolean',
        'can_approval' => 'boolean',
        'can_multi_companies' => 'boolean',
    ];

    public function subscribtionUsers()
    {
        return $this->hasMany(SubscribtionUser::class);
    }
}
