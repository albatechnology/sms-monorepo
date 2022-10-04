<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\Company;
use App\Models\Location;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('location_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            $query = Location::tenanted()->with('company')->select(sprintf('%s.*', (new Location)->table));
            $table = Datatables::of($query);
            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = false;
                $editGate      = 'location_edit';
                $deleteGate    = 'location_delete';
                $crudRoutePart = 'locations';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : "";
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : "";
            });
            $table->addColumn('company_name', function ($row) {
                return $row->company ? $row->company->name : '';
            });
            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }
        return view('admin.locations.index');
    }

    public function create()
    {
        abort_if(Gate::denies('location_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::tenanted()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        return view('admin.locations.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'orlan_id' => 'required|unique:locations,orlan_id',
            'name' => 'required|unique:locations',
            'company_id' => 'required|exists:companies,id',
        ]);
        Location::create($validated);
        return redirect()->route('admin.locations.index');
    }

    public function edit(location $location)
    {
        abort_if(Gate::denies('location_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::tenanted()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        return view('admin.locations.edit', compact('location','companies'));
    }

    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'orlan_id' => 'required|unique:locations,orlan_id,' . $location->id,
            'name' => 'required|unique:locations,name,' . $location->id,
            'company_id' => 'required|exists:companies,id',
        ]);
        $location->update($validated);
        return redirect()->route('admin.locations.index');
    }

    public function show(Location $location)
    {
        abort_if(Gate::denies('location_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('admin.locations.show', compact('location'));
    }

    public function destroy(Location $location)
    {
        abort_if(Gate::denies('location_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $location->delete();

        return back();
    }

    public function massDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array'
        ]);
        Location::whereIn('id', $request->ids)->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
