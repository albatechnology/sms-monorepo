<?php

namespace App\Console;

use App\Console\Commands\CloneNewMonthlyReportCommand;
use App\Console\Commands\ProcessImportBatch;
use App\Console\Commands\ReevaluateReports;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        ProcessImportBatch::class,
        ReevaluateReports::class,
        CloneNewMonthlyReportCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('horizon:snapshot')->everyFiveMinutes();

        $schedule->command('catchup:lead-status')->at('00:00');

        $schedule->command('queue:prune-batches')->daily();

        $schedule->command('telescope:prune --hours=48')->daily();

        // copy monthly report target every new month
        $schedule->command('report:clone-new-month-report')->monthly();
        // $schedule->command('new-reports:clone')->monthly();

        $schedule->job(new \App\Jobs\UnhandledLeadJob)->dailyAt('08:00');
        $schedule->job(new \App\Jobs\SendEmailReportLeads)->weeklyOn(1, '09:00')->timezone('Asia/Jakarta');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
