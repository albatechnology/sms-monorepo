<?php

namespace App\Console\Commands;

use App\Enums\TargetType;
use App\Models\Report;
use App\Models\Target;
use App\Services\ReportService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateTargetTypeForAllReports extends Command
{
    protected $signature = 'report:create-target-by-type {type : the target type to reevaluate}';

    protected $description = 'Create target of a given type to all existing reports';

    public function handle()
    {

        $type = $this->argument('type');

        try {
            $targetType = TargetType::fromKey($type);
        } catch (Exception $e) {
            $this->error('Invalid target type. Available options: ' . TargetType::getKeysString());
            return;
        }

        foreach (Report::all() as $report){

            dispatch(function () use ($report, $targetType) {
                Target::updateOrCreate(
                    [
                        'type'      => $targetType->value,
                        'report_id' => $report->id
                    ],
                    [
                        'model_type' => $report->reportable_type,
                        'model_id'   => $report->reportable_id,
                    ]
                );
            });

        }

        $this->info("Dispatched jobs to create target type {$targetType->value} on all reports.");
    }
}
