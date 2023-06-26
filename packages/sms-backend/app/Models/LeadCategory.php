<?php

namespace App\Models;

use App\Traits\IsSubscribedTenanted;
use App\Traits\SaveToSubscriber;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperPaymentCategory
 * @mixin IdeHelperLeadCategory
 */
class LeadCategory extends BaseModel
{
    use SoftDeletes, SaveToSubscriber, IsSubscribedTenanted;

    public $table = 'lead_categories';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $guarded = [];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
