<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscribtionUser extends Model
{
    protected $guarded = [];

    public function subscribtionPackage()
    {
        return $this->belongsTo(SubscribtionPackage::class);
    }
}
