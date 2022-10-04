<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyNewTargetRequest;
use App\Http\Requests\StoreNewTargetRequest;
use App\Http\Requests\UpdateNewTargetRequest;
use App\Models\NewTarget;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class NewTargetController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('new_target_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = NewTarget::with('model')->select(sprintf('%s.*', (new NewTarget)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                // $viewGate      = 'new_target_show';
                $editGate      = 'new_target_edit';
                $deleteGate    = 'new_target_delete';
                $crudRoutePart = 'new-targets';

                return view('partials.datatablesActions', compact(
                    // 'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ?? "";
            });
            $table->editColumn('target', function ($row) {
                return rupiah($row->target) ?? 0;
            });

            $table->addColumn('model_name', function ($row) {
                return $row->model?->name ?? "";
            });

            $table->editColumn('type', function ($row) {
                return $row->type?->key;
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.newTargets.index');
    }

    // public function create()
    // {
    //     abort_if(Gate::denies('new_target_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    //     // allow user to create type that has not been created for a report

    //     return view('admin.newTargets.create');
    // }

    // public function store(StoreNewTargetRequest $request)
    // {
    //     $new_target = NewTarget::create($request->validated());

    //     return redirect()->route('admin.new-targets.index');
    // }

    public function edit(NewTarget $new_target)
    {
        abort_if(Gate::denies('new_target_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.newTargets.edit', compact('new_target'));
    }

    public function update(UpdateNewTargetRequest $request, NewTarget $new_target)
    {
        $new_target->update($request->validated());

        return redirect()->route('admin.new-targets.index');
    }

    public function show(NewTarget $new_target)
    {
        abort_if(Gate::denies('new_target_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.newTargets.show', compact('new_target'));
    }

    public function destroy(NewTarget $new_target)
    {
        abort_if(Gate::denies('new_target_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $new_target->delete();

        return back();
    }

    public function massDestroy(MassDestroyNewTargetRequest $request)
    {
        NewTarget::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
