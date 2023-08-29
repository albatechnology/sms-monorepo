<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscribtionUser extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function subscribtionPackage()
    {
        return $this->belongsTo(SubscribtionPackage::class);
    }

    public function supervisorDiscountApprovalLimits()
    {
        return $this->hasMany(SupervisorDiscountApprovalLimit::class);
    }
}
