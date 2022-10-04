<?php

namespace App\Jobs;

use App\Models\ImportBatch;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MarkImportBatchAsFullyDispatchedJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected ImportBatch $batch)
    {

    }

    public function handle()
    {
        $this->batch->update(['all_jobs_added_to_batch_at' => now()]);
    }
}