<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupervisorTypeRequest;
use App\Http\Requests\UpdateSupervisorTypeRequest;
use App\Http\Resources\Admin\SupervisorTypeResource;
use App\Models\SupervisorType;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SupervisorTypeApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('supervisor_type_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new SupervisorTypeResource(SupervisorType::all());
    }

    public function store(StoreSupervisorTypeRequest $request)
    {
        $supervisorType = SupervisorType::create($request->all());

        return (new SupervisorTypeResource($supervisorType))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(SupervisorType $supervisorType)
    {
        abort_if(Gate::denies('supervisor_type_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new SupervisorTypeResource($supervisorType);
    }

    public function update(UpdateSupervisorTypeRequest $request, SupervisorType $supervisorType)
    {
        $supervisorType->update($request->all());

        return (new SupervisorTypeResource($supervisorType))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(SupervisorType $supervisorType)
    {
        abort_if(Gate::denies('supervisor_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $supervisorType->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
