<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Notifications\ActivityReminder;
use Illuminate\Console\Command;

/**
 * Class SendActivityReminder
 * @package App\Console\Commands
 */
class ScanActivityReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:activity-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan for activity reminder that is due and process then into queues';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        Activity::query()
            ->where('reminder_sent', false)
            ->whereTime('reminder_datetime', '<', now())
            ->with(['user'])
            ->get()
            ->each(function (Activity $activity) {
                $activity->user->notify(new ActivityReminder($activity));
            });
    }
}
