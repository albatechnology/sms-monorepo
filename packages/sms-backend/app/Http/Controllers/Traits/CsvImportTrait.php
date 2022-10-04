<?php

namespace App\Http\Controllers\Traits;

use App\Enums\Import\ImportBatchType;
use App\Rules\HasCompanyAccess;
use App\Services\FileImportService;
use BenSampo\Enum\Rules\EnumValue;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use SpreadsheetReader;

trait CsvImportTrait
{
    public function processCsvImport(Request $request)
    {
        try {
            $filename = $request->input('filename', false);
            $path     = storage_path('app/csv_import/' . $filename);

            $hasHeader = $request->input('hasHeader', false);

            $fields = $request->input('fields', false);
            $fields = array_flip(array_filter($fields));

            $modelName = $request->input('modelName', false);
            $model     = "App\Models\\" . $modelName;

            $company_id = $request->input('company_id', false);

            $reader = new SpreadsheetReader($path);
            $insert = [];

            foreach ($reader as $key => $row) {
                if ($hasHeader && $key == 0) {
                    continue;
                }

                $tmp = [];

                foreach ($fields as $header => $k) {
                    if (isset($row[$k])) {
                        $tmp[$header] = $row[$k];
                    }
                }

                if (count($tmp) > 0) {
                    $tmp['company_id'] = $company_id;
                    $insert[]          = $tmp;
                }
            }
            //ray($insert);

            $for_insert = array_chunk($insert, 100);

            foreach ($for_insert as $insert_item) {
                $model::insert($insert_item);
            }

            $rows  = count($insert);
            $table = Str::plural($modelName);

            File::delete($path);

            session()->flash('message', trans('global.app_imported_rows_to_table', ['rows' => $rows, 'table' => $table]));

            return redirect($request->input("redirect"));
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function parseCsvImport(Request $request)
    {
        $request->validate([
            'csv_file'   => 'mimes:csv,txt',
            'company_id' => 'required', new HasCompanyAccess,
            'type'       => 'required', new EnumValue(ImportBatchType::class),
        ]);

        // Create the import batch and line
        $import_batch = FileImportService::importFromRequest(
            ImportBatchType::fromValue((int)$request->get('type')),
            $request->get('company_id'),
            'csv_file',
        );

        return redirect()->route('admin.import-batches.show', $import_batch->id);
    }
}
