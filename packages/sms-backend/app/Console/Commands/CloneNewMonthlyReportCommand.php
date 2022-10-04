<?php

namespace App\Console\Commands;

use App\Services\ReportService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CloneNewMonthlyReportCommand extends Command
{
    protected $signature = 'report:clone-new-month-report
        {--month= : The month for the new reports. Uses current month when not provided.}
        {--year= : The year for the new reports. Uses current year when not provided.}
    ';

    protected $description = 'Clone previous monthly reports and targets for a new month.';

    public function handle()
    {

        $reportMonth = $this->option('month') ??  now()->month;
        $reportYear = $this->option('year') ??  now()->year;

        $startDate = Carbon::create($reportYear, $reportMonth);
        $endDate   = Carbon::create($reportYear, $reportMonth)->addMonth()->subSecond();

        // TODO: command option to choose the month to copy from
        $copyMonth = null;
        $copyYear  = null;

        if ($copyMonth && $copyYear) {
            // use the month as provided
            $copyStartDate = Carbon::create($copyYear, $copyMonth);
            $copyEndDate   = Carbon::create($copyYear, $copyMonth)->addMonth()->subSecond();
        } else {
            // use the month before the new report month
            $copyStartDate = Carbon::create($reportYear, $reportMonth)->subMonth();
            $copyEndDate   = Carbon::create($reportYear, $reportMonth)->subSecond();
        }

        $closure = function ($query) use ($copyStartDate, $copyEndDate) {
            return $query->where('start_date', '>=', $copyStartDate)
                ->where('end_date', '<=', $copyEndDate);
        };

        app(ReportService::class)->cloneReports($startDate, $endDate, $closure);

        $this->info('New monthly report cloning queued!');
    }
}
