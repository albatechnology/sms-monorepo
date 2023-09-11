<?php

namespace App\Services;

use App\Enums\ReportPipelineMode;
use App\Enums\TargetType;
use App\Interfaces\Reportable;
use App\Models\Activity;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Report;
use App\Models\Target;
use App\Models\TargetLine;
use App\Models\TargetMap;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pipeline\Pipeline;
use Carbon\Carbon;

class ReportService
{
    /**
     * Create default targets for a report.
     * @param Report $report
     */
    public function setupReport(Report $report): void
    {
        // create all default target type
        foreach (TargetType::getDefaultInstances() as $enum) {
            Target::updateOrCreate(
                [
                    'type'      => $enum,
                    'report_id' => $report->id
                ],
                [
                    'model_type' => $report->reportable_type,
                    'model_id'   => $report->reportable_id,
                ]
            );
        }
    }

    /**
     * Reevaluate a given report. Recalculate all of its target from scratch.
     * Only use to fix a previously broken data (i.e., due to requirement update)
     * @param Report $report
     */
    public function reevaluateReport(Report $report): void
    {
        $report->targets->each(function (Target $target) {
            $this->evaluateTarget($target);
        });
    }

    /**
     * Fetch all relevant data for the target to target map.
     * Typically used on newly create target or to force refresh
     * target
     * @param Target $target
     */
    public function evaluateTarget(Target $target): void
    {
        app(Pipeline::class)
            ->send(
                [
                    'target' => $target,
                    'mode'   => ReportPipelineMode::EVALUATE_TARGET
                ]
            )
            ->through(TargetType::allReportablePipes())
            ->thenReturn();
    }

    /**
     * Set a target for a given report.
     *
     * @param Report $report
     * @param TargetType $targetType
     * @param int $targetValue
     * @return Target
     */
    public function setTarget(Report $report, TargetType $targetType, int $targetValue): Target
    {
        $target = Target::firstOrCreate(
            [
                'report_id' => $report->id,
                'type'      => $targetType->value
            ]
        );

        $target->target = $targetValue;
        $target->save();

        return $target;
    }

    /**
     * A model that may or may not be relevant to an existing target.
     * If it relates to one or more target, add it to target map
     * @param Reportable $model
     * @throws Exception
     */
    public function registerTargetMap(Reportable $model): void
    {
        if ($model instanceof Order) {
            $reportableDate = $model->deal_at;
        }

        if ($model instanceof Payment) {
            $reportableDate = $model->order->deal_at ?? now();
        }

        if ($model instanceof Activity) {
            $reportableDate = $model->created_at ?? now();
        }

        if (!isset($reportableDate)) {
            throw new Exception("Unhandled reportable type {$model->getTable()}");
        }

        $reports = Report::query()
            ->with(['targets'])
            ->where('start_date', '<=', $reportableDate)
            ->where('end_date', '>=', $reportableDate)
            ->get();

        // now check if this model apply to any of this filtered down
        // targets
        $reports->each(function (Report $report) use ($model) {
            $report->targets->each(function (Target $target) use ($model) {
                $this->addTargetMap($model, $target);
            });
        });

    }

    /**
     * Map model against a target if relevant
     * @param Reportable $model
     * @param Target $target
     */
    protected function addTargetMap(Reportable $model, Target $target): void
    {
        app(Pipeline::class)
            ->send(
                [
                    'target' => $target,
                    'model'  => $model,
                    'mode'   => ReportPipelineMode::ADD_TARGET_MAP
                ]
            )
            ->through(TargetType::allReportablePipes())
            ->thenReturn();
    }

    /**
     * Called when new target map is created.
     * Add calculation towards target
     * @param TargetMap $targetMap
     */
    public function newTargetMapAdded(TargetMap $targetMap): void
    {
        $target = $targetMap->target;

        app(Pipeline::class)
            ->send(
                [
                    'target'     => $target,
                    'mode'       => ReportPipelineMode::ADD_NEW_TARGET_MAP_TO_TARGET,
                    'target_map' => $targetMap
                ]
            )
            ->through(TargetType::allReportablePipes())
            ->thenReturn();
    }

    /**
     * Remove an reportable model from all target using it and then reevaluate the
     * target. This can be optimised to not reevaluate all target, by
     * decrementing the target value instead. But this can be quite complex.
     * @param Reportable $model
     */
    public function removeModelFromTargetMap(Reportable $model): void
    {

        // grab the target to update before target map deletion
        $targets = Target::query()
            ->whereHas('targetMap', function ($query) use ($model) {
                $query->where('model_type', get_class($model))
                    ->where('model_id', $model->id);
            })
            ->get();

        // delete the target map
        TargetMap::query()
            ->where('model_type', get_class($model))
            ->where('model_id', $model->id)
            ->delete();

        // Now queue job to re evaluate each affected target
        dispatch(function () use ($targets) {
            $targets->each(function (Target $target) {
                app(ReportService::class)->evaluateTarget($target);
            });
        });
    }

    public function setTargetLineTarget(Target $target, Model $model, int $targetValue): TargetLine
    {
        return TargetLine::updateOrCreate(
            [
                'target_id'  => $target->id,
                'model_type' => get_class($model),
                'model_id'   => $model->id,
            ],
            [
                'target' => $targetValue
            ]
        );
    }

    /**
     * Clone reports and its targets.
     * The target value of the target objects will be copied across as well.
     * @param Carbon $start_date
     * @param Carbon $end_date
     * @param callable|null $closure build query for a range of report to be cloned
     */
    public function cloneReports(Carbon $start_date, Carbon $end_date, callable $closure = null): void
    {
        $query = Report::query();

        if ($closure) {
            $query = $closure($query);
        }

        $query->chunk(50, function($reports) use ($start_date, $end_date) {

            foreach($reports as $report){

                $startTime = $start_date->timestamp;
                $endTime = $end_date->timestamp;

                // queue each clone
                dispatch(function () use ($report, $startTime, $endTime) {

                    // create report
                    $newReport = Report::create([
                        'reportable_type' => $report->reportable_type,
                        'reportable_id'   => $report->reportable_id,
                        'start_date'      => $startTime,
                        'end_date'        => $endTime,
                    ]);

                    // target lines of the report would be automatically created
                    // we just need to copy the target value of previous targets to the new targets
                    $newReport->targets->each(function (Target $target) use ($report){
                        // find the matching target
                        $matchingTarget = $report->targets->first(fn(Target $t) => $t->type->is($target->type));

                        // update if target is not 0 (since 0 is default)
                        if($matchingTarget->target !== 0){
                            $target->target = $matchingTarget->target;
                            $target->save();
                        }
                    });

                    // could possibly go as a new job
                    // $this->reevaluateReport($newReport);
                });
            }
        });
    }
}
