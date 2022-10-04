<?php

namespace App\Jobs;

use App\Enums\LeadStatus;
use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LeadStatusChange implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Lead $lead, protected LeadStatus $from_status)
    {

    }

    public function handle()
    {
        $this->lead->nextStatusAndqueue($this->from_status);
    }
}