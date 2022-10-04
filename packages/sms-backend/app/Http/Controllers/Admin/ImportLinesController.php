<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Import\ImportBatchType;
use App\Enums\Import\ImportLinePreviewStatus;
use App\Enums\Import\ImportLineStatus;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ImportLine;
use Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ImportLinesController extends Controller
{
    public function index($batch_id, Request $request)
    {
        abort_if(Gate::denies('import_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ImportLine::tenanted()
                               ->where('import_batch_id', $batch_id)
                               ->select(sprintf('%s.*', (new ImportLine)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) use ($batch_id) {
                // only show edit button when status is preview
                $editGate      = $row->status->is(ImportLineStatus::PREVIEW);
                $editRoute     = route('admin.import-batches.import-lines.edit', [$batch_id, $row]);
                $crudRoutePart = 'import-batches.import-lines';

                return view('partials.datatablesActions', compact(
                    'editGate',
                    'editRoute',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('row', function ($row) {
                return $row->row ?? "";
            });
            $table->editColumn('status', function ($row) {
                if (!$row->status) return '';

                $class = match ($row->status->value) {
                    ImportLineStatus::PREVIEW => 'secondary',
                    ImportLineStatus::CREATED => 'success',
                    ImportLineStatus::UPDATED => 'primary',
                    ImportLineStatus::SKIPPED => 'warning',
                    ImportLineStatus::ERROR => 'danger',
                };

                return sprintf(
                    '<span class="external-event bg-%s">%s</span>',
                    $class,
                    $row->status->description
                );
            });
            $table->editColumn('preview_status', function ($row) {

                if (!$row->preview_status) return '';

                $class = match ($row->preview_status->value) {
                    ImportLinePreviewStatus::NONE => 'secondary',
                    ImportLinePreviewStatus::ERROR => 'danger',
                    ImportLinePreviewStatus::NEW => 'primary',
                    ImportLinePreviewStatus::DUPLICATE => 'warning',
                };

                return sprintf(
                    '<span class="external-event bg-%s">%s</span>',
                    $class,
                    $row->preview_status->description
                );
            });
            $table->editColumn('errors', function ($row) {
                $labels = [];

                foreach ($row->errors ?? [] as $error) {
                    $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $error);
                }

                return implode("<br>", $labels);
            });
            $table->editColumn('name', function ($row) {
                return $row->data['name'] ?? "";
            });
            $table->editColumn('code', function ($row) {
                return $row->data['code'] ?? "";
            });

            $table->rawColumns(['status', 'errors', 'preview_status', 'actions', 'placeholder']);

            return $table->make(true);
        }

        return null;
    }

    public function create()
    {
        abort_if(Gate::denies('import_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::tenanted()->get()->pluck('name', 'id');
        $types     = ImportBatchType::getInstances();

        return view('admin.importBatches.create', compact('companies', 'types'));
    }



    public function edit($batch_id, ImportLine $import_line)
    {
        abort_if(Gate::denies('import_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.importLines.edit', compact('batch_id', 'import_line'));
    }

    /**
     * This doesn't actually update the line, just the data property
     * @param Request $request
     * @param $import_line
     * @return RedirectResponse
     */
    public function update(Request $request, $import_batch_id, ImportLine $import_line)
    {
        unset($request['_token'], $request['_method']);

        $import_line->data = $request->all();
        $import_line->evaluatePreview();
        $import_line->save();

        $import_line->importBatch->refreshSummary();

        return redirect()->route('admin.import-batches.show', $import_batch_id);
    }


}
