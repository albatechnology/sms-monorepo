<?php

namespace App\Jobs;

use App\Enums\Import\ImportBatchStatus;
use App\Models\ImportBatch;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Clean up at the end of import batch imports.
 * Here we run post import validation, as well as marking model as processed.
 * Class PostImportBatch
 * @package App\Jobs
 */
class PostImportBatchJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected ImportBatch $batch)
    {
    }

    public function handle()
    {
        $this->batch->getImporter()->postImportValidation();
        $this->batch->refreshSummary();
        $this->batch->update(['status' => ImportBatchStatus::PREVIEW]);
    }
}