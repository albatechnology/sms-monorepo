<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubLeadCategory extends Model
{
    protected $guarded = [];
    public $table = 'sub_lead_categories';

    public function leadCategory()
    {
        return $this->belongsTo(LeadCategory::class, 'lead_category_id');
    }
}
