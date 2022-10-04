<?php

namespace App\Services;

use App\Classes\ImportManager;
use App\Contracts\ExceptionMessage;
use App\Enums\Import\ImportBatchStatus;
use App\Enums\Import\ImportBatchType;
use App\Enums\Import\ImportMode;
use App\Jobs\MarkImportBatchAsFullyDispatchedJob;
use App\Jobs\ProcessImportBatchJob;
use App\Jobs\ProcessImportLineJob;
use App\Models\ImportBatch;
use App\Models\ImportLine;
use Exception;
use Illuminate\Support\Facades\Bus;
use Throwable;


class FileImportService
{

    /**
     * @param ImportBatchType $type
     * @param int $company_id
     * @param string $file
     * @return ?ImportBatch
     * @throws Exception
     */
    public static function importFromRequest(
        ImportBatchType $type,
        int $company_id,
        string $file = 'file',
    ): ?ImportBatch
    {
        // Grab file from request
        $file = request()->file($file);
        if (empty($file)) {
            throw new Exception(sprintf(ExceptionMessage::ImportFileMissingFromRequest, $file));
        }

        $import = new ImportManager($file, $type, $company_id);
        $import->preview();

        return $import->getBatch();
    }

    /**
     * @param ImportBatch $importBatch
     * @param ImportMode $mode
     * @throws Throwable
     */
    public static function processImportBatch(ImportBatch $importBatch, ImportMode $mode)
    {
        if ($importBatch->status->isNot(ImportBatchStatus::PREVIEW)) {
            return;
        }

        $batch = Bus::batch([])
            ->allowFailures()
            ->finally(function () use ($importBatch) {
                if (!$importBatch->refresh()->all_jobs_added_to_batch_at) {
                    return;
                }

                $importBatch->refreshSummary();
                $importBatch->update(['status' => ImportBatchStatus::FINISHED]);
                $importBatch->getImporter()->afterBatchImport();
            })
            ->onQueue('import')
            ->dispatch();

        $importBatch->update(
            [
                'job_batch_id' => $batch->id,
                'mode'         => $mode,
                'status'       => ImportBatchStatus::IMPORTING
            ]
        );

//        ImportLine::query()
//            ->where('import_batch_id', $importBatch->id)
//            ->cursor()
//            ->map(fn(ImportLine $importLine) => new ProcessImportLineJob($importLine, $importBatch))
//            ->filter()
//            ->each(fn(ProcessImportLineJob $job) => $batch->add([$job]));

        ImportLine::query()
            ->where('import_batch_id', $importBatch->id)
            ->chunk(200, function ($importLines) use ($batch) {
                $data = collect([]);
                foreach ($importLines as $importLine) {
                    $job = new ProcessImportLineJob($importLine);
                    $data->push($job->onQueue('import'));
                }
                $batch->add($data->all());
            });

        $batch->add([new MarkImportBatchAsFullyDispatchedJob($importBatch)]);
    }

    /**
     * @param ImportBatch $importBatch
     * @param ImportMode $mode
     * @throws Throwable
     */
    public static function processBulkImportBatch(ImportBatch $importBatch, ImportMode $mode)
    {
        $importBatch->update(['mode' => $mode]);

        $batch = Bus::batch([])
            ->allowFailures()
            ->finally(function () use ($importBatch) {
                if (!$importBatch->refresh()->all_jobs_added_to_batch_at) {
                    return;
                }

                $importBatch->refreshSummary();
                $importBatch->update(['status' => ImportBatchStatus::FINISHED]);
                $importBatch->getImporter()->afterBatchImport();
            })
            ->onQueue('import')
            ->dispatch();

        $importBatch->update(['job_batch_id' => $batch->id]);

        $lineCount = ImportLine::where('import_batch_id', $importBatch->id)->count();

        $chunk = 200;
        $count = $lineCount + 1;

        $iteration = (int)floor($count / $chunk);

        $ranges = collect(range(1, $iteration))->map(
            function ($r) use ($chunk) {
                return [
                    'fromRow' => (($r - 1) * $chunk) + 1,
                    'toRow'   => $r * $chunk
                ];
            }
        );

        $overflow = $count % $chunk;

        if ($overflow != 0) {
            $ranges->push(
                [
                    'fromRow' => $count - $overflow + 1,
                    'toRow'   => $count
                ]
            );
        }

        $ranges->each(function (array $range) use ($importBatch, $batch) {
            $job = new ProcessImportBatchJob($importBatch, $range['fromRow'], $range['toRow']);
            $batch->add([$job->onQueue('import')]);
        });

        $batch->add([new MarkImportBatchAsFullyDispatchedJob($importBatch)]);
    }
}
