<?php

namespace App\Imports;

use App\Enums\Import\ImportLinePreviewStatus;
use App\Enums\Import\ImportLineStatus;
use App\Jobs\PostImportBatchJob;
use App\Models\ImportBatch;
use App\Models\ImportLine;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;

abstract class BaseBulkImport implements WithBatchInserts, WithChunkReading, ShouldQueue, WithEvents
{
    use Importable, RemembersRowNumber, RegistersEventListeners;

    public function __construct(protected ImportBatch $batch)
    {

    }

    /**
     * Model class to be imported
     * @return string
     */
    abstract public static function getModelName(): string;

    public static function getUniqueKey(): string
    {
        return 'id';
    }

    /**
     * array of headers
     * @return array
     */
    abstract public static function getHeader(): array;

    /**
     * Dispatch post import job, validation, summary  and status update
     * @param AfterImport $event
     */
    public static function afterImport(AfterImport $event)
    {
        PostImportBatchJob::dispatch($event->getConcernable()->batch);
    }

    /**
     * May provide export sample data
     * @return Collection
     */
    public static function getExportCollection(): Collection
    {
        return Collect([]);
    }

    /**
     * This gets executed after the completion of the whole batch import
     */
    public function afterBatchImport()
    {

    }

    /**
     * Post validation after all csv data has been imported into import lines.
     * By default we are validation against unique_key being used multiple times
     * within the import file.
     * @return void
     */
    public function postImportValidation(): void
    {
//        $lines = ImportLine::query()
//            ->where('import_batch_id', $this->batch->id)
//            ->get(['id', 'row']);
//
//        $duplicates = $lines->groupBy('unique_key')
//            ->filter(function (Collection $groups) {
//                return $groups->count() > 1;
//            });
//
//        if ($duplicates->isEmpty()) return;
//
//        // duplicates are collection of collections of import lines
//        $duplicates->each(function (Collection $groups) {
//
//            // iterate into each group, to assign errors to all line with duplicates
//            $groups->each(function (ImportLine $line) use ($groups) {
//
//                $line->errors = $groups
//                    // filter out matching with self
//                    ->filter(function (ImportLine $subLine) use ($line) {
//                        return $subLine->id != $line->id;
//                    })
//                    // map duplicate line into error
//                    ->map(function (ImportLine $line) {
//                        return sprintf(
//                            '%s %s is duplicate with row %s',
//                            static::getUniqueKey(),
//                            $line->unique_key,
//                            $line->row
//                        );
//                    })
//                    ->all();
//
//                $line->preview_status = ImportLinePreviewStatus::ERROR();
//                $line->save();
//            });
//        });
    }

    /**
     * Given a row data, create the import line
     * @param array $row
     * @return Model|null
     */
    public function model(array $row)
    {
        $line = new ImportLine([
            'status'          => ImportLineStatus::PREVIEW(),
            'row'             => $this->getRowNumber(),
            'errors'          => [],
            'data'            => $row,
            'import_batch_id' => $this->batch->id,
            'company_id'      => $this->batch->company_id,
        ]);

        $this->validateLine($line);

        return $line;
    }

    /**
     * Validate the line, modifying errors and preview_status property
     * as appropriate
     * @param ImportLine $line
     */
    public function validateLine(ImportLine $line)
    {
        $line->errors = [];

        // check duplicate code with existing model
//        if ($model = $this->models->get($line->data[static::getUniqueKey()])) {
//            if ($model->deleted_at) {
//                $line->addError(
//                    sprintf('Row %s %s has been used on deleted model, please change the %s.',
//                        static::getUniqueKey(),
//                        $line->data[static::getUniqueKey()],
//                        static::getUniqueKey()
//                    )
//                );
//            } else {
//                $line->preview_status = ImportLinePreviewStatus::DUPLICATE();
//            }
//        } else {
//            $line->preview_status = ImportLinePreviewStatus::NEW();
//        }
        $line->preview_status = ImportLinePreviewStatus::NEW();

        $validator = Validator::make($line->data, $this->getValidationRule($line->data));
        $line->addErrors($validator->errors()->all());

        if ($line->hasError()) {
            $line->preview_status = ImportLinePreviewStatus::ERROR();
        }
    }

    /**
     * Validation rule for the data
     * @return array
     */
    abstract protected function getValidationRule(array $data): array;

    /**
     * Process the import line to model.
     * This will be called from ProcessImportLineJob
     * @param ImportLine $line
     */
    public function process(ImportBatch $batch, int $fromRow, int $toRow)
    {
        $query = ImportLine::query()
            ->where('import_batch_id', $batch->id)
            ->whereBetween('row', [$fromRow, $toRow])
            ->where('status', ImportLineStatus::PREVIEW);

        $lines = $query->get();

        // re-validate each line

        $lines
            ->each(function (ImportLine $line) {
                $this->validateLine($line);

                if (!$line->preview_status->in([ImportLinePreviewStatus::NEW, ImportLinePreviewStatus::DUPLICATE])) {
                    $line->updateStatus(ImportLineStatus::SKIPPED());
                }
            })
            ->filter(function (ImportLine $line) {
                return $line->preview_status->in([ImportLinePreviewStatus::NEW, ImportLinePreviewStatus::DUPLICATE]);
            });

        if ($lines->isEmpty()) {
            return;
        }

        // for now we just do bulk insert
        DB::beginTransaction();

        try {
            $this->insertModel($lines);
            $query->update(['status' => ImportLineStatus::CREATED]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $query->update(
                [
                    'status' => ImportLineStatus::ERROR,
                    'errors' => ['Unexpected error! This import line has not been processed.']
                ]
            );
            Log::error($e->getMessage());
        }

    }

    /**
     * Create model based on import line
     * @param Collection $lines
     * @return mixed
     */
    protected function insertModel(Collection $lines): void
    {
        (static::getModelName())::insert($this->insertArray($lines));
    }

    /**
     * The insert array
     * @param Collection $lines
     * @return mixed
     */
    abstract protected function insertArray(Collection $lines): array;

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    /**
     * Update existing model based on import line
     * @param ImportLine $line
     * @return mixed
     */
    protected function updateModel(ImportLine $line): void
    {
        (static::getModelName())::query()
            ->where(static::getUniqueKey(), $line->data[static::getUniqueKey()])
            ->where('company_id', $this->batch->company_id)
            ->firstOrFail()
            ->update(static::updateArray($line));
    }

    /**
     * The update array
     * @param ImportLine $line
     * @return mixed
     */
    abstract protected function updateArray(ImportLine $line): array;

    /**
     * Create model based on import line
     * @param ImportLine $line
     * @return mixed
     */
    protected function createModel(ImportLine $line): void
    {
        (static::getModelName())::create($this->createArray($line));
    }

    /**
     * The create array
     * @param ImportLine $line
     * @return mixed
     */
    abstract protected function createArray(ImportLine $line): array;
}
