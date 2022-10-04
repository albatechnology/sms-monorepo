<?php

namespace App\Jobs;

use App\Models\ImportBatch;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessImportBatchJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected ImportBatch $batch, protected int $fromRow, protected int $toRow)
    {
    }

    public function handle()
    {
        if (optional($this->batch())->canceled()) {
            // optionally perform some clean up if necessary
            return;
        }

        $this->batch->getImporter()->processBatch($this->batch, $this->fromRow, $this->toRow);
    }

//    /**
//     * Handle a job failure.
//     *
//     * @param Throwable $exception
//     * @return void
//     */
//    public function failed(Throwable $exception)
//    {
//        $this->line->addError('Internal error. Please try again or contact developer.');
//        $this->line->exception_message = $exception->getMessage();
//        $this->line->updateStatus(ImportLineStatus::ERROR());
//    }
}