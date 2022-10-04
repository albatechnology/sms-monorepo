<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImportBatch;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ImportBatchesController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('import_management_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ImportBatch::tenanted()->select(sprintf('%s.*', (new ImportBatch)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'import_show';
                $crudRoutePart = 'import-batches';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ?? "";
            });
            $table->editColumn('type', function ($row) {
                return $row->type?->description ?? "";
            });
            $table->editColumn('filename', function ($row) {
                return $row->filename ?? "";
            });
            $table->editColumn('status', function ($row) {
                return $row->status?->description ?? "";
            });
            $table->editColumn('summary', function ($row) {
                return $row->summary ?? "";
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.importBatches.index');
    }

    public function show($batch)
    {
        abort_if(Gate::denies('import_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $batch = ImportBatch::tenanted()->with('importLines')->findOrFail($batch);
        return view('admin.importBatches.show', compact('batch'));
    }
}
