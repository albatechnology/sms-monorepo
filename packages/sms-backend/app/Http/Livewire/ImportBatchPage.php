<?php

namespace App\Http\Livewire;

use App\Enums\Import\ImportBatchStatus;
use App\Enums\Import\ImportMode;
use App\Jobs\GenericQueueJob;
use App\Models\ImportBatch;
use App\Services\FileImportService;
use Illuminate\Queue\SerializableClosure;
use Illuminate\Support\Facades\Bus;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Class ImportBatchPage
 * @property ImportBatch $batch
 * @package App\Http\Livewire
 */
class ImportBatchPage extends Component
{
    public int $progress = 0;
    public bool $shouldPoll = true;
    public bool $stopPoll = false;

    public $batch;
    protected $jobBatch = null;

    protected $lastProgress = 0;
    protected $stuckCounter = 0;

    public int $status;

    public function mount()
    {
        $this->status = $this->batch->status->value;
        $this->checkPoll();
    }

    /**
     * Determine whether the page should be polling.
     * Note that we cannot directly set shouldPoll to false to
     * stop polling as livewire will not update the latest changes.
     * Instead when we want to stop polling, we set stopPoll to true,
     * which is used on the next polling iteration to eventually stop polling.
     */
    protected function checkPoll()
    {
        // check if we receive a command to stop polling
        if ($this->shouldPoll == true && $this->stopPoll == true) {
            $this->shouldPoll = false;
            $this->stopPoll   = false;
            return;
        }

        // check if we should poll based on import status
        $shouldPoll = ImportBatchStatus::fromValue($this->status)->in(
            [
                ImportBatchStatus::GENERATING_PREVIEW,
                ImportBatchStatus::IMPORTING,
            ]
        );

        if ($shouldPoll == false) {
            // if we need to stop polling, set the stop poll command
            $this->stopPoll = true;
        } else {
            $this->shouldPoll = true;
            $this->stopPoll   = false;
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|string
     */
    public function render()
    {
        return view('livewire.import-batch-page');
    }

    public function poll()
    {
        //if status changed
        if ($this->batch->status->value != $this->status) {
            $this->status = $this->batch->status->value;
            $this->emit('reloadImportLineTable');
        }

        // get progress update
        $this->checkProgressUpdate();

        $this->checkPoll();
    }

    public function startPoll()
    {
        $this->shouldPoll = true;
        $this->stopPoll   = false;
    }

    protected function checkProgressUpdate()
    {
        if (!$this->batch->status->isImporting()) return;

        // try to load job batch model if not yet loaded
        $this->loadJobBatch();

        if (!$this->jobBatch) return;

        $this->jobBatch = $this->jobBatch->fresh();
        $this->updateProgress($this->jobBatch->progress());
    }

    /**
     * If job batch has not been set, we set it
     */
    public function loadJobBatch()
    {
        if ($this->jobBatch) return;

        $uuid = $this->batch->job_batch_id;
        if (!$uuid) return;

        $this->jobBatch = Bus::findBatch($uuid);
    }

    protected function updateProgress($newProgress)
    {
        if ($newProgress == $this->progress) {
            $this->stuckCounter++;

            // update the batch every 5 poll without progress
            if ($this->stuckCounter % 5 == 4) {
                $this->batch->refresh();
            }
        } else {
            $this->progress     = $newProgress;
            $this->stuckCounter = 0;
        }
    }

    /**
     * cancel
     */
    public function cancelImport()
    {
        $this->batch->cancel();
        $this->batch->refresh();
    }

    /**
     * initiate import, skip dulplicate
     */
    public function processSkip()
    {
        $this->process(ImportMode::SKIP_DUPLICATE());
    }

    /**
     * initiate import, update duplicate
     */
    public function processUpdate()
    {
        $this->process(ImportMode::UPDATE_DUPLICATE());
    }

    /**
     * initiate import, update duplicate
     */
    public function processBulkInsert()
    {
        $this->process(ImportMode::BULK_INSERT());
    }

    /**
     * Initiate import for the import batch
     * @param ImportMode $mode
     */
    protected function process(ImportMode $mode)
    {
        if ($this->batch->status->isNot(ImportBatchStatus::PREVIEW)) {
            return;
        }

        if ($mode->is(ImportMode::BULK_INSERT())) {
            $importBatch = new SerializableClosure(fn() => FileImportService::processBulkImportBatch($this->batch, $mode));
        } else {
            $importBatch = new SerializableClosure(fn() => FileImportService::processImportBatch($this->batch, $mode));
        }

        GenericQueueJob::dispatch($importBatch)->onQueue('import-high');

        $this->startPoll();
    }
}