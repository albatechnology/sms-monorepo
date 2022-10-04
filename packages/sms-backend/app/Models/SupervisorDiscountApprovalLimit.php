<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupervisorDiscountApprovalLimit extends Model
{
    protected $primaryKey = null;
    public $incrementing = false;

    protected $guarded = [];
    public $timestamps = false;
    protected $casts = [
        'limit' => 'integer',
    ];
}
