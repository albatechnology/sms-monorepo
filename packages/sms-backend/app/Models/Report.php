<?php

namespace App\Models;

use App\Enums\ReportableType;
use App\Interfaces\Tenanted;
use App\Services\ReportService;
use App\Traits\Auditable;
use App\Traits\IsTenanted;
use Carbon\Carbon;
use Exception;

/**
 * @mixin IdeHelperReport
 */
class Report extends BaseModel implements Tenanted
{
    use Auditable, IsTenanted;

    protected $dates = [
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'start_date',
        'end_date',
        'name',
        'reportable_type',
        'reportable_id',
        'reportable_label',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'reportable_type' => ReportableType::class,
        'reportable_id'   => 'integer',
        'time_diff'       => 'integer',
    ];

    /**
     *  Setup model event hooks.
     */
    public static function boot()
    {
        self::creating(function (self $model) {
            if (empty($model->reportable_label)) {
                $model->reportable_label = $model->reportable->getReportLabel();
            }

            if (empty($model->name)) {
                $model->name = $model->makeReportName();
            }
        });

        self::created(function (self $model) {
            app(ReportService::class)->setupReport($model);
        });

        self::saving(function (self $model) {
            $startTimeCarbon = new Carbon($model->start_date);
            $endTimeCarbon   = new Carbon($model->end_date);

            $model->time_diff = $startTimeCarbon->diffInRealSeconds($endTimeCarbon);
        });

        parent::boot();
    }

    /**
     * Override tenanted trait method scope
     * @param $query
     * @return mixed
     * @throws Exception
     */
    public function scopeTenanted($query)
    {
        $user = tenancy()->getUser();

        if ($user->is_director || $user->is_admin) {
            return $query;
        }

        $query = $query->where(function ($q) use ($user) {
            $q->where('reportable_type', ReportableType::USER)
                ->whereIn('reportable_id', User::descendantsAndSelf($user->id)->pluck('id'));
        });

        // supervisor may see channel
        if ($user->is_supervisor && $user->channel_id) {
            $query = $query->orWhere(function ($q) use ($user) {

                $channelIds = Channel::query()->tenanted()->get(['id'])->pluck('id');

                $q->where('reportable_type', ReportableType::CHANNEL)
                    ->whereIn('reportable_id', $channelIds->all());
            });
        }

        return $query;
    }

    public function getStartDateAttribute($value)
    {
        if (!$value) return null;

        $value = Carbon::createFromFormat('Y-m-d H:i:s', $value);
        return is_api() ? $value->toISOString() : $value->format(config('panel.date_format') . ' ' . config('panel.time_format'));
    }

    //    public function setStartDateAttribute($value)
    //    {
    //        $this->attributes['start_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    //    }

    public function getEndDateAttribute($value)
    {
        if (!$value) return null;

        $value = Carbon::createFromFormat('Y-m-d H:i:s', $value);
        return is_api() ? $value->toISOString() : $value->format(config('panel.date_format') . ' ' . config('panel.time_format'));
    }

    //    public function setEndDateAttribute($value)
    //    {
    //        $this->attributes['end_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    //    }

    public function reportable()
    {
        return $this->morphTo('reportable');
    }

    public function targets()
    {
        return $this->hasMany(Target::class);
    }

    public function scopePeriodBefore($query, $datetime)
    {
        return $query->where('start_date', '<=', Carbon::parse($datetime));
    }

    public function scopePeriodAfter($query, $datetime)
    {
        return $query->where('end_date', '>=', Carbon::parse($datetime));
    }

    public function makeReportName()
    {
        // if ($this->reportable_type->is(ReportableType::COMPANY)) {
        if ($this->reportable_type->is(ReportableType::SUBSCRIBTION_USER)) {
            return sprintf(
                '%s %s',
                $this->reportable->getReportLabel(),
                Carbon::make($this->start_date)->format('F Y'),
            );
        }

        if ($this->reportable_type->is(ReportableType::CHANNEL)) {
            return sprintf(
                '%s - %s',
                $this->reportable->subscribtionUser->getReportLabel(),
                $this->reportable->getReportLabel(),
            );
        }

        if ($this->reportable_type->is(ReportableType::USER)) {
            return sprintf(
                '%s - %s - %s',
                $this->reportable->subscribtionUser->getReportLabel(),
                strtoupper($this->reportable->getReportLabel()),
                Carbon::make($this->start_date)->format('F Y'),
            );
        }

        return sprintf(
            '%s (%s - %s)',
            $this->reportable->getReportLabel(),
            $this->start_date,
            $this->end_date
        );
    }
}
