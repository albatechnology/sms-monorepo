<?php

namespace App\Models;

use App\Traits\Auditable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

/**
 * @mixin IdeHelperTargetSchedule
 */
class TargetSchedule extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'target_schedules';

    const TARGET_SUBJECT_TYPE_SELECT = [
        'sum'  => 'Sum',
        'each' => 'Each',
    ];

    protected $dates = [
        'start_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    const TARGET_VALUE_TYPE_SELECT = [
        'order-net'   => 'Order  price',
        'order-count' => 'Count',
    ];

    const TARGET_SCOPE_MODEL_SELECT = [
        'company' => 'Company',
        'channel' => 'Channel',
        'global'  => 'Global',
    ];

    const TARGET_SUBJECT_SELECT = [
        'company'    => 'Company',
        'channel'    => 'Channel',
        'supervisor' => 'Supervisor',
        'sales'      => 'Sales',
    ];

    const DURATION_TYPE_SELECT = [
        'annual'    => 'Annual',
        'quarterly' => 'Quarterly',
        'monthly'   => 'Monthly',
        'biweekly'  => 'Bi-Weekly',
        'weekly'    => 'Weekly',
        'daily'     => 'Daily',
    ];

    const TARGET_TYPE_SELECT = [
        'income'   => 'Income',
        'promo'    => 'Promo',
        'activity' => 'Activity',
        'deal'     => 'Deal',
        'discount' => 'Discount',
        'category' => 'Category',
        'product'  => 'Product',
    ];

    protected $fillable = [
        'duration_type',
        'start_date',
        'target_name',
        'value',
        'target_value_type',
        'target_type',
        'target_type_identifier',
        'target_subject',
        'target_subject_type',
        'target_scope_model',
        'target_scope_identifier',
        'custom_scope_json',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getStartDateAttribute($value)
    {
        if(!$value) return null;

        $value = Carbon::createFromFormat('Y-m-d H:i:s', $value);
        return is_api() ? $value->toISOString() : $value->format(config('panel.date_format') . ' ' . config('panel.time_format'));
    }

    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }
}
