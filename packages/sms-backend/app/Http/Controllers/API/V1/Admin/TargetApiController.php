<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTargetRequest;
use App\Http\Requests\UpdateTargetRequest;
use App\Http\Resources\Admin\TargetResource;
use App\Models\Target;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TargetApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('target_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new TargetResource(Target::with(['orders', 'order_details'])->get());
    }

    public function store(StoreTargetRequest $request)
    {
        $target = Target::create($request->all());
        $target->orders()->sync($request->input('orders', []));
        $target->order_details()->sync($request->input('order_details', []));

        return (new TargetResource($target))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Target $target)
    {
        abort_if(Gate::denies('target_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new TargetResource($target->load(['orders', 'order_details']));
    }

    public function update(UpdateTargetRequest $request, Target $target)
    {
        $target->update($request->all());
        $target->orders()->sync($request->input('orders', []));
        $target->order_details()->sync($request->input('order_details', []));

        return (new TargetResource($target))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Target $target)
    {
        abort_if(Gate::denies('target_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $target->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
