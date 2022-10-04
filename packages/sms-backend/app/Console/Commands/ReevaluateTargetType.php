<?php

namespace App\Console\Commands;

use App\Enums\TargetType;
use App\Models\Target;
use App\Services\ReportService;
use Exception;
use Illuminate\Console\Command;

class ReevaluateTargetType extends Command
{
    protected $signature = 'report:reevaluate-target-type {type : the target type to reevaluate}';

    protected $description = 'Reevaluate all target of a certain target type';

    public function handle()
    {
        $type = $this->argument('type');

        try {
            $targetType = TargetType::fromKey($type);
        } catch (Exception $e) {
            $this->error('Invalid target type. Available options: ' . TargetType::getKeysString());
            return;
        }

        Target::where('type', $targetType->value)
            ->get()
            ->each(function (Target $target) {

                dispatch(function () use ($target) {
                    app(ReportService::class)->evaluateTarget($target);
                });

            });

        $this->info("All {$targetType->key} targets are dispatched for reevaluation");
    }
}
