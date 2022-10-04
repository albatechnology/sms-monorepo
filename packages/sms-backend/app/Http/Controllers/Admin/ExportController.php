<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Import\ImportBatchType;
use App\Exports\ImportSampleExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ModelExportRequest;
use App\Models\Company;
use App\Models\Export;
use App\Services\CoreService;
use Exception;
use Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ExportController extends Controller
{
    /**
     * @param string $type
     * @return BinaryFileResponse
     * @throws Exception
     */
    public function sample(string $type)
    {
        $type = ImportBatchType::fromValue((int)$type);

        return Excel::download(new ImportSampleExport($type), 'sample.csv');
    }

    public function index()
    {
        abort_if(Gate::denies('import_management_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::tenanted()->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $exports = Export::all();
        if (request()->ajax()) {
            $data = Export::orderBy('created_at', 'desc')->get();
            return datatables()::of($data)
                ->addColumn('placeholder', '&nbsp;')
                ->addColumn('actions', '&nbsp;')
                ->editColumn('user_id', function ($data) {
                    return $data->user->name ?? '-';
                })
                ->editColumn('status', function ($data) {
                    if ($data->status == 0) {
                        return 'On Progress';
                    } elseif ($data->status == 1) {
                        return 'Success';
                    } elseif ($data->status == 2) {
                        return 'Failed';
                    } else {
                        return 'On Progress';
                    }
                })
                ->addColumn('file_download', function ($data) {
                    $html = '';
                    foreach ($data->file as $media) {
                        $html = '<a href="' . $media->getUrl() . '" target="_blank" class="btn btn-danger"><i class="fa fa-download"></i> Download</a>';
                    }
                    return $html;
                })
                ->editColumn('created_at', function ($data) {
                    return date('D, d-m-Y H:i', strtotime($data->created_at));
                })
                ->editColumn('done_at', function ($data) {
                    return $data->done_at ? date('D, d-m-Y H:i', strtotime($data->created_at)) : '';
                })
                ->editColumn('actions', function ($row) {
                    return '<form action="' . route('admin.exports.destroy', $row->id) . '" method="POST"
                    onsubmit="return confirm(' . trans('global.areYouSure') . ');" style="display: inline-block;">
                  <input type="hidden" name="_method" value="DELETE">
                  <input type="hidden" name="_token" value="' . csrf_token() . '">
                  <input type="submit" class="btn btn-xs btn-danger" value="' . trans('global.delete') . '">
              </form>';
                })
                ->rawColumns(['actions', 'placeholder', 'file_download'])
                ->make(true);
        }

        return view('admin.export.index', compact('companies', 'exports'));
    }


    public function model(ModelExportRequest $request)
    {
        $type = ImportBatchType::fromValue($request->get('type'));

        $model = $type->getModel();
        $table = (new $model())->getTable();

        //        $without = ['created_at', 'updated_at', 'deleted_at'];
        //        $headers = array_diff(Schema::getColumnListing($table), $without);
        //
        $query = DB::table($table)->orderBy('id');

        if ($startId = $request->get('start_id')) {
            $query = $query->where('id', '>=', $startId);
        }

        if ($startId = $request->get('end_id')) {
            $query = $query->where('id', '<=', $startId);
        }

        $headers = Schema::getColumnListing($table);

        return app(CoreService::class)->genericModelExport($query, $table);
    }

    public function destroy($id)
    {
        Export::destroy($id);

        return back();
    }
}
