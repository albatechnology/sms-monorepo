<?php

namespace App\Exports;

use App\Enums\Import\ImportBatchType;
use App\Models\BaseModel;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class ImportSampleExport implements FromCollection
{
    protected string $model_class;

    public function __construct(protected ImportBatchType $type)
    {
        $this->model_class = $type->getModel();
        if (!is_a($this->model_class, BaseModel::class, true)) {
            throw new Exception('Model property on ImportSampleExport must extend from BaseModel');
        }
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        $sample   = $this->model_class::factory()->sample()->make();
        $importer = $this->type->getImporter();

        // export collection could be provided on import class
        $data = $importer::getExportCollection();
        if ($data->isNotEmpty()) return $data;

        // otherwise, derive from header and factory
        $header = $importer::getHeader();
        return collect([$header])
            ->push(
                collect($header)
                    ->map(function ($key) use ($sample) {
                        return $sample->$key;
                    })
                    ->all()
            );
    }
}
