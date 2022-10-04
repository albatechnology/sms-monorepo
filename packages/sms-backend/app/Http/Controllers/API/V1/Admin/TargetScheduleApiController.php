<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTargetScheduleRequest;
use App\Http\Requests\UpdateTargetScheduleRequest;
use App\Http\Resources\Admin\TargetScheduleResource;
use App\Models\TargetSchedule;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TargetScheduleApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('target_schedule_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new TargetScheduleResource(TargetSchedule::all());
    }

    public function store(StoreTargetScheduleRequest $request)
    {
        $targetSchedule = TargetSchedule::create($request->all());

        return (new TargetScheduleResource($targetSchedule))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(TargetSchedule $targetSchedule)
    {
        abort_if(Gate::denies('target_schedule_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new TargetScheduleResource($targetSchedule);
    }

    public function update(UpdateTargetScheduleRequest $request, TargetSchedule $targetSchedule)
    {
        $targetSchedule->update($request->all());

        return (new TargetScheduleResource($targetSchedule))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(TargetSchedule $targetSchedule)
    {
        abort_if(Gate::denies('target_schedule_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $targetSchedule->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
