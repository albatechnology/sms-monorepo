<?php

namespace App\Console\Commands;

use App\Models\Lead;
use Exception;
use Illuminate\Console\Command;

/**
 * Class CatchUpMissedLeadStatusChange
 * @package App\Console\Commands
 */
class CatchUpMissedLeadStatusChange extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'catchup:lead-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan for missed lead status update and update them';

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
     * @throws Exception
     */
    public function handle()
    {
        Lead::query()
            ->where('has_pending_status_change', 1)
            ->where('status_change_due_at', '<', now())
            ->get()
            ->each(function (Lead $lead) {
                $lead->nextStatusAndQueue();
            });
    }
}
