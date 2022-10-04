<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyTargetScheduleRequest;
use App\Http\Requests\StoreTargetScheduleRequest;
use App\Http\Requests\UpdateTargetScheduleRequest;
use App\Models\TargetSchedule;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TargetScheduleController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('target_schedule_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $targetSchedules = TargetSchedule::all();

        return view('admin.targetSchedules.index', compact('targetSchedules'));
    }

    public function create()
    {
        abort_if(Gate::denies('target_schedule_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.targetSchedules.create');
    }

    public function store(StoreTargetScheduleRequest $request)
    {
        $targetSchedule = TargetSchedule::create($request->validated());

        return redirect()->route('admin.target-schedules.index');
    }

    public function edit(TargetSchedule $targetSchedule)
    {
        abort_if(Gate::denies('target_schedule_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.targetSchedules.edit', compact('targetSchedule'));
    }

    public function update(UpdateTargetScheduleRequest $request, TargetSchedule $targetSchedule)
    {
        $targetSchedule->update($request->validated());

        return redirect()->route('admin.target-schedules.index');
    }

    public function show(TargetSchedule $targetSchedule)
    {
        abort_if(Gate::denies('target_schedule_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.targetSchedules.show', compact('targetSchedule'));
    }

    public function destroy(TargetSchedule $targetSchedule)
    {
        abort_if(Gate::denies('target_schedule_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $targetSchedule->delete();

        return back();
    }

    public function massDestroy(MassDestroyTargetScheduleRequest $request)
    {
        TargetSchedule::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
