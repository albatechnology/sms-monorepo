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

    public function scopeTenanted($query): mixed
    {
        $user = tenancy()->getUser();
        $hasActiveChannel = tenancy()->getActiveTenant();
        // $hasActiveCompany = tenancy()->getActiveCompany();
        $isAdmin          = $user->is_admin;

        if ($isAdmin) return $query;
        if ($hasActiveChannel) return $query->where('id', $hasActiveChannel->subscribtionUser->id);
        if (!$isAdmin) return $query->where('id', $user->subscribtion_user_id);
        // if ($hasActiveCompany) return $query->where('id', $hasActiveCompany->id);

        // return $query->whereIn('id', auth()->user()->subscribtion_user_ids ?? []);
        return $query->where('id', $user->subscribtion_user_id ?? null);
        // return $query;
    }

    public function subscribtionPackage()
    {
        return $this->belongsTo(SubscribtionPackage::class);
    }

    public function supervisorDiscountApprovalLimits()
    {
        return $this->hasMany(SupervisorDiscountApprovalLimit::class);
    }

    public function getReportLabel(): string
    {
        return $this->name;
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }
}
