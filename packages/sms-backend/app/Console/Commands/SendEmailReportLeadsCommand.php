<?php

namespace App\Console\Commands;

use App\Jobs\SendEmailReportLeads;
use Illuminate\Console\Command;

class SendEmailReportLeadsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:send-email-report-leads {emails? : user emails}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send report leads to Director and BUM Moves';

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
     * @return int
     */
    public function handle()
    {
        $emails = [];
        if ($this->argument('emails') !== null) {
            $emails = \Illuminate\Support\Str::of($this->argument('emails'))->explode(',')->toArray();
        }

        SendEmailReportLeads::dispatch($emails);
        $this->info('Sending email...!');
    }
}
