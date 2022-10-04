<?php

namespace App\Jobs;

use App\Enums\Import\ImportLineStatus;
use App\Models\ImportBatch;
use App\Models\ImportLine;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ProcessImportLineJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected ImportLine $line, protected ?ImportBatch $batch = null)
    {
        $this->batch = $this->batch ?? $line->importBatch;
    }

    public function handle()
    {
        if (optional($this->batch())->canceled()) {
            // optionally perform some clean up if necessary
            return;
        }

        $this->batch->getImporter()->process($this->line);
    }

    /**
     * Handle a job failure.
     *
     * @param Throwable $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        $this->line->addError('Internal error. Please try again or contact developer.');
        $this->line->exception_message = $exception->getMessage();
        $this->line->updateStatus(ImportLineStatus::ERROR());
    }
}