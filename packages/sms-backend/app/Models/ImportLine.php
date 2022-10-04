<?php

namespace App\Models;

use App\Enums\Import\ImportLinePreviewStatus;
use App\Enums\Import\ImportLineStatus;
use App\Imports\BaseImport;
use App\Interfaces\Tenanted;
use App\Traits\HasErrors;
use App\Traits\IsCompanyTenanted;

/**
 * @mixin IdeHelperImportLine
 */
class ImportLine extends BaseModel implements Tenanted
{
    use IsCompanyTenanted, HasErrors;

    public $table = 'import_lines';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'status',
        'preview_status',
        'row',
        'errors',
        'data',
        'import_batch_id',
        'exception_message',
        'company_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'status'          => ImportLineStatus::class,
        'preview_status'  => ImportLinePreviewStatus::class,
        'row'             => 'integer',
        'errors'          => 'array',
        'data'            => 'array',
        'import_batch_id' => 'integer',
        'company_id'      => 'integer',
        'id'              => 'integer',
    ];

    public function importBatch()
    {
        return $this->belongsTo(ImportBatch::class);
    }

    /**
     * Evaluate preview status based on current data.
     * Call when import line is updated and should be re-validated
     */
    public function evaluatePreview()
    {
        if ($this->status->isNot(ImportLineStatus::PREVIEW)) {
            return;
        }

        $this->getImporter()->validateLine($this);
    }

    public function getImporter(): BaseImport
    {
        return $this->importBatch->getImporter();
    }

    /**
     * Import preview line to model
     */
    public function process()
    {
        if ($this->status->isNot(ImportLineStatus::PREVIEW)) return;

        $this->getImporter()->process($this);
    }

    /**
     * Helper function to set this import line as skipped
     * @param ImportLineStatus $status
     * @param array $attributes
     */
    public function updateStatus(ImportLineStatus $status, array $attributes = [])
    {
        $this->update(array_merge([
            'status' => $status,
        ], $attributes));
    }
}