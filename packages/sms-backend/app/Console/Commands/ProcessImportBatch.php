<?php

namespace App\Console\Commands;

use App\Enums\Import\ImportBatchStatus;
use App\Enums\Import\ImportMode;
use App\Models\ImportBatch;
use App\Services\FileImportService;
use Exception;
use Illuminate\Console\Command;
use Throwable;

class ProcessImportBatch extends Command
{
    protected $signature = 'import-batch:process {id} {mode}';

    protected $description = 'Command description';

    public function handle()
    {
        $id   = $this->argument('id');
        $mode = $this->argument('mode');

        $batch = ImportBatch::find($id);

        if (!$batch) {
            $this->error("Unknown import batch id {$id}");
            return;
        }

        if ($batch->status->isNot(ImportBatchStatus::PREVIEW)) {
            $this->error("Import batch id {$id} is not on status PREVIEW.");
            return;
        }

        try {
            $mode = ImportMode::fromValue((int)$mode);
        } catch (Exception $e) {
            $this->error("Unknown import mode {$mode}. Available option:");
            foreach (ImportMode::getInstances() as $enum) {
                $this->error("- {$enum->key} => {$enum->value}");
            }
            return;
        }

        try {
            FileImportService::processBulkImportBatch($batch, $mode);
        } catch (Throwable $e) {
            $this->error("Failed to process import: {$e->getMessage()}");
            return;
        }

        $this->info('Import job is now processing!');
    }
}
