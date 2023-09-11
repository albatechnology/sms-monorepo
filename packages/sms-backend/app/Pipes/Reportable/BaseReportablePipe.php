<?php

namespace App\Pipes\Reportable;

use App\Classes\TargetMapContext;
use App\Enums\ReportableType;
use App\Enums\ReportPipelineMode;
use App\Enums\TargetChartType;
use App\Enums\TargetType;
use App\Interfaces\Reportable;
use App\Models\Report;
use App\Models\Target;
use App\Models\TargetLine;
use App\Models\TargetMap;
use App\Models\User;
use Closure;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 *
 * Class DealsInvoicePrice
 * @package App\Pipes\Discountable
 */
abstract class BaseReportablePipe
{
    protected Target $target;
    protected Report $report;
    protected ?Reportable $model;
    protected ?TargetMap $targetMap;
    protected array $reportablePipelineSetting;

    // region abstracts

    /**
     * Starting point of the pipe
     * @param array $reportablePipelineSetting
     * @param Closure $next
     * @return array|mixed
     * @throws Exception
     */
    public function handle(array $reportablePipelineSetting, Closure $next)
    {
        $this->reportablePipelineSetting = $reportablePipelineSetting;
        $this->target                    = $reportablePipelineSetting['target'];
        $this->report                    = $this->target->report;
        $this->model                     = $reportablePipelineSetting['model'] ?? null;
        $this->targetMap                 = $reportablePipelineSetting['target_map'] ?? null;

        // simply skip this pipe if it does not match the intended target type
        if ($this->target->type->isNot($this->getTargetType())) {
            return $next($reportablePipelineSetting);
        }

        $mode = ReportPipelineMode::fromValue($reportablePipelineSetting['mode']);

        if ($mode->is(ReportPipelineMode::ADD_TARGET_MAP)) {
            return $this->addTargetMap($reportablePipelineSetting, $next);
        }

        if ($mode->is(ReportPipelineMode::EVALUATE_TARGET)) {
            return $this->evaluateTarget($reportablePipelineSetting, $next);
        }

        if ($mode->is(ReportPipelineMode::ADD_NEW_TARGET_MAP_TO_TARGET)) {
            return $this->addTargetMapToTarget($reportablePipelineSetting, $next);
        }

        return $reportablePipelineSetting;
    }

    /**
     * The target type that the pipe handle
     * @return TargetType
     */
    abstract protected function getTargetType(): TargetType;

    /**
     * Base method to add target map.
     * @param array $reportablePipelineSetting
     * @param Closure $next
     * @return mixed
     * @throws Exception
     */
    public function addTargetMap(array $reportablePipelineSetting, Closure $next): mixed
    {
        // validate for error and scope
        if (!$this->addTargetMapValidation()) {
            return $next($reportablePipelineSetting);
        }

        $this->createTargetMap();

        return $next($reportablePipelineSetting);
    }

    protected function addTargetMapValidation(): bool
    {
        // model must be provided to add target map
        if (!$this->model) {
            throw new Exception('Model must be provided to run report pipeline. Target type: ' . $this->getTargetType()->value);
        }

        // make sure that the model passed to the pipe is the expected model
        if (!(is_a($this->model, $this->getReportableClassName(), true))) {
            return false;
        }

        // order made for different company
        // if ($this->report->reportable_type->is(ReportableType::COMPANY) && $this->model->company_id !== $this->report->reportable_id) {
        //     return false;
        // }

        // order made for different channel
        if ($this->report->reportable_type->is(ReportableType::CHANNEL) && $this->model->channel_id !== $this->report->reportable_id) {
            return false;
        }

        // order made for different user
        if ($this->report->reportable_type->is(ReportableType::USER)) {

            // check whether this apply to this user or its descendants
            $applyToUser = User::descendantsAndSelf($this->report->reportable_id)
                ->where('id', $this->model->user_id)
                ->count();

            if ($applyToUser === 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * The class name of the reportable model for the
     * target type handled by this pipe
     * @return string
     */
    abstract protected function getReportableClassName(): string;


    //endregion


    // region main logic

    /**
     * The main logic to create new target map
     */
    protected function createTargetMap(): void
    {
        TargetMap::create([
            'target_id'  => $this->target->id,
            'model_type' => get_class($this->model),
            'model_id'   => $this->model->id,
            'value'      => $this->getReportableValue(),
            'context'    => $this->getTargetContext()
        ]);
    }

    /**
     * The target value to be set to the target map
     * @param Reportable|null $model
     * @return int
     */
    protected function getReportableValue(Reportable $model = null): int
    {
        if (!$model) {
            return $this->model[$this->getReportableValueProperty()];
        }

        return $model[$this->getReportableValueProperty()];
    }

    /**
     * Name of the model property to be added to the value property on target
     * @return string|null
     */
    abstract protected function getReportableValueProperty(): ?string;

    /**
     * return the context should be stored on the target
     * @param Reportable|null $model
     * @return ?array
     */
    protected function getTargetContext(?Reportable $model = null): ?array
    {
        return null;
    }

    /**
     * Method to re evaluate the value of a target, recreating the
     * whole target maps
     * @param array $reportablePipelineSetting
     * @param Closure $next
     * @return mixed
     */
    protected function evaluateTarget(array $reportablePipelineSetting, Closure $next): mixed
    {
        $class = $this->getReportableClassName();

        $query = $this->whereReportableBaseQuery($class::query());

        // scope to company
        // if ($this->report->reportable_type->is(ReportableType::COMPANY)) {
        //     $query = $this->whereReportableCompany($query, $this->target->model_id);
        // }

        // scope to channel
        if ($this->report->reportable_type->is(ReportableType::CHANNEL)) {
            $query = $this->whereReportableChannel($query, $this->target->model_id);
        }

        // scope to user and its descendants
        if ($this->report->reportable_type->is(ReportableType::USER)) {
            $query = $this->whereReportableUsers($query, $this->getUserAndDescendantIds());
        }

        // fetch id as minimum
        $fields = ['id'];

        // add the sum field is applicable
        if ($this->getReportableValueProperty()) {
            $fields[] = $this->getReportableValueProperty();
        }

        $models = $query->get($fields);


        // now we delete and re create to target map
        TargetMap::where('target_id', $this->target->id)->delete();

        $targetMapData = $models->map(function (Reportable $model) {
            return [
                'model_type' => $this->getReportableClassName(),
                'model_id'   => $model->id,
                'target_id'  => $this->target->id,
                'value'      => $this->getReportableValue($model),
                'context'    => json_encode($this->getTargetContext($model))
            ];
        });

        foreach($targetMapData->chunk(200) as $chunk){
            TargetMap::insert($chunk->all());
        }

        // update the value on target too
        $this->target->value = $targetMapData->pluck('value')->sum();
        $this->target->save();

        // if the chart type is multiple, we need to update the target lines instead
        if ($this->target->type->getChartType()->is(TargetChartType::MULTIPLE)) {

            // reset target line value to 0
            TargetLine::query()
                ->where('target_id', $this->target->id)
                ->update(['value' => 0]);

            $data = [];

            // we group the context in the target map by their ids,
            // combining the values of the same model
            TargetMap::query()
                ->where('target_id', $this->target->id)
                ->get()
                ->map(function (TargetMap $map) {
                    return $map->context;
                })
                ->collapse()
                ->each(function ($contextArray) use (&$data) {
                    $id = $contextArray['id'];

                    $context = TargetMapContext::fromArray($contextArray);

                    // if there is existing context data
                    if ($data[$id] ?? false) {
                        // need to sum with existing data
                        $context = $data[$id]->combine($context);
                    }

                    $data[$id] = $context;
                });

            // after the value is combined, we update the target line value
            collect($data)->each(function (TargetMapContext $context) {

                TargetLine::updateOrCreate(
                    [
                        'model_type' => $context->class,
                        'model_id'   => $context->id,
                        'target_id'  => $this->target->id,
                    ],
                    [
                        'value' => $context->value,
                        'label' => $context->label
                    ]
                );

            });

        }

        return $next($reportablePipelineSetting);
    }

    //endregion

    //region helpers

    /**
     * Scope the reportable model to only applicable row for the report
     * @param $query
     * @param int $id
     */
    abstract protected function whereReportableBaseQuery($query);

    /**
     * Scope the reportable model to only a given company id
     * @param $query
     * @param int $id
     * @return mixed
     */
    protected function whereReportableCompany($query, int $id)
    {
        return $query->where('company_id', $id);
    }

    /**
     * Scope the reportable model to only a given channel id
     * @param $query
     * @param int $id
     */
    abstract protected function whereReportableChannel($query, int $id);

    /**
     * Scope the reportable model to only a given array of user ids
     * @param $query
     * @param array $ids if of user for the report and its descendants
     */
    abstract protected function whereReportableUsers($query, array $ids);

    /**
     * Helper method, returns the id of an user and its descendants that a target
     * is assigned to. The reportable type of the target must be USER
     * @return mixed
     */
    protected function getUserAndDescendantIds(): array
    {
        if ($this->report->reportable_type->isNot(ReportableType::USER)) {
            return [];
        }

        return User::descendantsAndSelf($this->target->model_id)->pluck('id')->all();
    }

    /**
     * Method to add a target map to target
     * @param array $reportablePipelineSetting
     * @param Closure $next
     * @return void
     */
    protected function addTargetMapToTarget(array $reportablePipelineSetting, Closure $next): void
    {
        DB::transaction(function () {

            // increment by value
            DB::table('targets')
                ->where('id', $this->targetMap->target_id)
                ->increment('value', $this->targetMap->value);

            // for non single chart, we also need to update the target lines
            if ($this->target->type->getChartType()->is(TargetChartType::MULTIPLE)) {

                // TODO: this can be optimise so that we fetch all TargetLines
                //   in 1 sql query. But this may be okay as the number of loop is
                //   dependant on the number of unique brand/model in one order
                foreach ($this->targetMap->context ?? [] as $context) {
                    $model_type = $this->target->type->getTargetLineModelClass();
                    $model_id   = $context['id'];
                    $label      = $context['label'];
                    $value      = $context['value'];

                    $targetLine = TargetLine::query()
                        ->where('model_type', $model_type)
                        ->where('model_id', $model_id)
                        ->where('target_id', $this->target->id)
                        ->first();

                    if ($targetLine) {
                        $value += $targetLine->value;
                    }

                    TargetLine::updateOrCreate(
                        [
                            'model_type' => $model_type,
                            'model_id'   => $model_id,
                            'target_id'  => $this->target->id,
                        ],
                        [
                            'label' => $label,
                            'value' => $value
                        ]
                    );

                }
            }
        });
    }

    //endregion
}
