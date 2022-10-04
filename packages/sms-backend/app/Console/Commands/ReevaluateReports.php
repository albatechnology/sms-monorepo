<?php

namespace App\Console\Commands;

use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ReevaluateReports extends Command
{
    protected $signature = 'report:reevaluate-reports {ids? : the report ids, comma separated}';

    protected $description = 'Reevaluate reports and all of its targets';

    public function handle()
    {

        $query = Report::query();

        if ($this->argument('ids') !== null) {
            $ids = Str::of($this->argument('ids'))->explode(',');
            $query = $query->whereIn('id', $ids);
        }

        $query->get()->each(function (Report $report) {
            dispatch(function () use ($report) {
                app(ReportService::class)->reevaluateReport($report);
            });
        });

        $this->info('Reports are dispatched for re-evaluation.');
    }
}
