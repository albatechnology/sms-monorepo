<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscribtionUser extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($model) {
            SupervisorType::select('id')->get()->each(function ($supervisorType) use ($model) {
                $model->supervisorDiscountApprovalLimits()->create([
                    'supervisor_type_id' => $supervisorType->id,
                    'limit' => 0
                ]);
            });
        });
    }

    public function subscribtionPackage()
    {
        return $this->belongsTo(SubscribtionPackage::class);
    }

    public function supervisorDiscountApprovalLimits()
    {
        return $this->hasMany(SupervisorDiscountApprovalLimit::class);
    }
}
