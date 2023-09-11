<?php

namespace App\Models;

use App\Enums\TargetChartType;
use App\Enums\TargetType;
use App\Traits\Auditable;
use App\Traits\IsTenanted;
use Carbon\Carbon;
use Exception;

/**
 * @mixin IdeHelperTarget
 */
class Target extends BaseModel
{
    use Auditable, IsTenanted;

    public $table = 'targets';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'name',
        'model_type',
        'model_id',
        'report_id',
        'type',
        'target',
        'value',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'target'     => 'integer',
        'value'      => 'integer',
        'model_id'   => 'integer',
        'report_id'  => 'integer',
        'type'       => TargetType::class,
        'chart_type' => TargetChartType::class,
        'context'    => 'json',
    ];


    /**
     *  Setup model event hooks.
     */
    public static function boot()
    {
        self::created(function (self $model) {
            // $job = static function () use ($model) {
            //     app(ReportService::class)->evaluateTarget($model);
            // };

            // GenericQueueJob::dispatch(new SerializableClosure($job));
        });

        parent::boot();
    }

    /**
     * Get the parent model, may be null
     */
    public function model()
    {
        return $this->morphTo('model');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'model_id', 'id')->where('model_type', 'user');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class, 'model_id', 'id')->where('model_type', 'channel');
    }

    public function targetMap()
    {
        return $this->hasMany(TargetMap::class);
    }

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Target access is based on user's report access
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

        $ids = Report::tenanted()->get(['id'])->pluck('id');
        return $query->whereIn('report_id', $ids->all());
    }

    public function getChartTypeAttribute($value): TargetChartType
    {
        return $this->type?->getChartType() ?? TargetChartType::SINGLE();
    }

    public function targetLines()
    {
        return $this->hasMany(TargetLine::class);
    }

    public function target_lines()
    {
        return $this->hasMany(TargetLine::class);
    }

    public function getTargetFormattedAttribute()
    {
        return $this->type->isPrice() ? rupiah($this->target) : $this->target;
    }

    public function getValueFormattedAttribute($value)
    {
        return $this->type->isPrice() ? rupiah($this->value) : $this->value;
    }

    public function scopeTargetDatetime($query, $datetime)
    {
        return $query->whereHas('report', function ($q) use ($datetime) {
            $q->where('start_date', '<=', Carbon::parse($datetime))
                ->where('end_date', '>=', Carbon::parse($datetime));
        });
    }

    public function scopeStartAfter($query, $datetime)
    {
        return $query->whereHas('report', function ($q) use ($datetime) {
            if (isset(request()->filter['is_dashboard']) && request()->filter['is_dashboard'] == 1) {
                $q->whereDate('start_date', Carbon::parse($datetime));
            } else {
                $datetime = Carbon::createFromFormat('Y-m-d', $datetime)->startOfDay();
                $q->where('start_date', '>=', Carbon::parse($datetime));
            }
        });
    }

    public function scopeEndBefore($query, $datetime)
    {
        return $query->whereHas('report', function ($q) use ($datetime) {
            if (isset(request()->filter['is_dashboard']) && request()->filter['is_dashboard'] == 1) {
                $q->whereDate('end_date', Carbon::parse($datetime));
            } else {
                $datetime = Carbon::createFromFormat('Y-m-d', $datetime)->endOfDay();
                $q->where('end_date', '<=', Carbon::parse($datetime));
            }
        });
    }

    public function scopeWhereReportableIds($query, ...$ids)
    {
        return $query->whereHas('report', function ($q) use ($ids) {
            $q->whereIn('reportable_id', $ids);
        });
    }

    public function scopeWhereReportableType($query, $type)
    {
        return $query->whereHas('report', function ($q) use ($type) {
            $q->where('reportable_type', $type);
        });
    }

    public function scopeWhereDescendantOf($query, $id)
    {
        $ids = User::whereDescendantOf($id)->get(['id'])->pluck('id')->all();

        return $query->whereReportableIds(...$ids);
    }

    public function scopeIsDashboard($query)
    {
        return;
    }

    public function scopeWhereCompanyId($query, $id)
    {
        return $query->whereHas('model', function ($q) use ($id) {
            $q->whereCompanyId($id);
        });
    }

    public function scopeWhereSupervisorTypeLevel($query, $id)
    {
        return $query
            ->where('model_type', 'user')
            ->whereHas('model', function ($q) use ($id) {
                $q->whereSupervisorTypeLevel($id);
            });
    }

    public function getBreakdown()
    {
    }
}
