<?php

namespace App\Models;

use App\Enums\Import\ImportBatchStatus;
use App\Enums\Import\ImportBatchType;
use App\Enums\Import\ImportMode;
use App\Imports\BaseBulkImport;
use App\Imports\BaseImport;
use App\Interfaces\Tenanted;
use App\Traits\HasErrors;
use App\Traits\IsCompanyTenanted;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Bus;

/**
 * @mixin IdeHelperImportBatch
 */
class ImportBatch extends BaseModel implements Tenanted
{
    use IsCompanyTenanted, HasErrors;

    public $table = 'import_batches';

    protected $dates = [
        'all_jobs_added_to_batch_at',
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'filename',
        'status',
        'type',
        'mode',
        'summary',
        'preview_summary',
        'errors',
        'company_id',
        'user_id',
        'job_batch_id',
        'all_jobs_added_to_batch_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'user_id'    => 'integer',
        'status'     => ImportBatchStatus::class,
        'type'       => ImportBatchType::class,
        'mode'       => ImportMode::class,
        'errors'     => 'array',
    ];

    public function refreshSummary()
    {
        $lines = $this->importLines()->get(['status', 'preview_status']);

        $this->summary = $lines
            ->pluck('status')
            ->countBy(fn($enum) => $enum->description)
            ->map(function ($count, $key) {
                return sprintf('(%s) %s', $count, strtolower($key));
            })
            ->implode(', ');

        $this->preview_summary = $lines
            ->pluck('preview_status')
            ->countBy(fn($enum) => $enum->description)
            ->map(function ($count, $key) {
                return sprintf('(%s) %s', $count, strtolower($key));
            })
            ->implode(', ');

        $this->save();
    }

    /**
     * @return HasMany
     */
    public function importLines(): HasMany
    {
        return $this->hasMany(ImportLine::class);
    }

    /**
     * @return bool
     */
    public function processable(): bool
    {
        return $this->status->is(ImportBatchStatus::PREVIEW) && is_null($this->all_jobs_added_to_batch_at);
    }

    /**
     * @return bool
     */
    public function processing(): bool
    {
        return $this->status->in([ImportBatchStatus::PREVIEW, ImportBatchStatus::GENERATING_PREVIEW]);
    }

    public function cancel()
    {
        if ($this->status->cancellable()) {
            $this->update(['status' => ImportBatchStatus::CANCELLED]);
        }
    }


    public function getImporter(): BaseImport|BaseBulkImport
    {
        $importer = $this->type->getImporter();
        return new $importer($this);
    }

    /**
     * @return Batch|null
     */
    public function getJobBatch(): ?Batch
    {
        if (!$this->job_batch_id) {
            return null;
        }

        return Bus::findBatch($this->job_batch_id);
    }

}