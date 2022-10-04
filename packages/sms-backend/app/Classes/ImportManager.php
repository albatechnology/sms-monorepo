<?php

namespace App\Classes;

use App\Enums\Import\ImportBatchStatus;
use App\Enums\Import\ImportBatchType;
use App\Models\ImportBatch;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class ImportManager
{
    protected Collection $errors;
    protected string $importer_class;
    protected array $heading;
    protected ImportBatch $batch;

    /**
     * ImportManager constructor.
     * @param UploadedFile $file
     * @param ImportBatchType $type
     * @param int $company_id
     * @throws Exception
     */
    public function __construct(
        protected UploadedFile $file,
        protected ImportBatchType $type,
        protected int $company_id,
    )
    {
        $this->loadImporterClass();

        $this->errors = collect([]);

        $this->validateHeader();

        $this->createImportBatch();
    }

    /**
     * @throws Exception
     */
    protected function loadImporterClass()
    {
        // Load importer class
        $importer = $this->type->getImporter();
//        if (!$importer || !is_a($importer, BaseImport::class, true)) {
//            throw new Exception(sprintf(ExceptionMessage::ImportBatchTypeMissingImporterClass, $this->type->value));
//        }

        $this->importer_class = $importer;
    }

    /**
     * Validate that this
     */
    protected function validateHeader()
    {
        $headings       = (new HeadingRowImport)->toArray($this->file);
        $missing_header = collect($this->importer_class::getHeader())->diff($headings[0][0]);

        if ($missing_header->isEmpty()) return;

        $missing_header->each(function (string $header) {
            $this->errors = $this->errors
                ->push(sprintf('Header "%s" are missing from file.', $header));
        });
    }

    protected function createImportBatch()
    {
        $status = $this->errors->isEmpty() ? ImportBatchStatus::READY_TO_IMPORT_LINES() : ImportBatchStatus::ERROR_PRE_LOAD();

        $this->batch = ImportBatch::create(
            [
                'filename'   => $this->file->getClientOriginalName(),
                'status'     => $status,
                'type'       => $this->type,
                'errors'     => $this->errors->all(),
                'company_id' => $this->company_id,
                'user_id'    => user()->id,
            ]
        );
    }

    /**
     * Start the import process.
     * @throws Exception
     */
    public function preview()
    {
        if ($this->batch->status->isNot(ImportBatchStatus::READY_TO_IMPORT_LINES)) {
            return;
        }

        $this->batch->update(['status' => ImportBatchStatus::GENERATING_PREVIEW]);

        try {
            $import_class = new $this->importer_class($this->batch);
            Excel::import($import_class, $this->file);
        } catch (Exception $e) {
            $this->batch->update(
                [
                    'status' => ImportBatchStatus::ERROR_PROCESSING,
                    'errors' => collect($this->batch->errors)->push($e->getMessage())
                ]
            );
            throw $e;
        }
    }

    /**
     * @return Collection
     */
    public function getErrors(): Collection
    {
        return $this->errors ?? collect([]);
    }

    public function getBatch(): ?ImportBatch
    {
        return $this->batch;
    }
}
