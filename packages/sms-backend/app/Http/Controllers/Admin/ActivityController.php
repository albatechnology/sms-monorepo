<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyActivityRequest;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ActivityController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('activity_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Activity::tenanted()->with(['user', 'lead'])
                ->select(sprintf('%s.*', (new Activity)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'activity_show';
                $editGate      = 'activity_edit';
                $deleteGate    = 'activity_delete';
                $crudRoutePart = 'activities';

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
            $table->addColumn('user_name', function ($row) {
                return $row->user ? $row->user->name : '';
            });

            $table->addColumn('lead_label', function ($row) {
                return $row->lead ? $row->lead->label : '';
            });

            $table->editColumn('follow_up_method', function ($row) {
                return $row->follow_up_method?->description ?? '';
            });

            $table->editColumn('feedback', function ($row) {
                return $row->feedback ? $row->feedback : "";
            });
            $table->editColumn('status', function ($row) {
                return $row->status?->description ?? '';
            });

            $table->rawColumns(['actions', 'placeholder', 'user', 'lead']);

            return $table->make(true);
        }

        return view('admin.activities.index');
    }

    public function create()
    {
        abort_if(Gate::denies('activity_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::tenanted()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $leads = Lead::tenanted()->get()->pluck('label', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.activities.create', compact('users', 'leads'));
    }

    public function store(StoreActivityRequest $request)
    {
        $activity = Activity::create($request->validated());

        return redirect()->route('admin.activities.index');
    }

    public function edit(Activity $activity)
    {
        abort_if(Gate::denies('activity_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::tenanted()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $leads = Lead::tenanted()->pluck('label', 'id')->prepend(trans('global.pleaseSelect'), '');

        $customers = Customer::tenanted()->pluck('full_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $activity->load('user', 'lead');

        return view('admin.activities.edit', compact('users', 'leads', 'activity', 'customers'));
    }

    public function update(UpdateActivityRequest $request, Activity $activity)
    {
        $activity->update($request->validated());

        return redirect()->route('admin.activities.index');
    }

    public function show(Activity $activity)
    {
        abort_if(Gate::denies('activity_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $activity->load('user', 'lead', 'activityActivityComments');

        return view('admin.activities.show', compact('activity'));
    }

    public function destroy(Activity $activity)
    {
        abort_if(Gate::denies('activity_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $activity->delete();

        return back();
    }

    public function massDestroy(MassDestroyActivityRequest $request)
    {
        Activity::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
