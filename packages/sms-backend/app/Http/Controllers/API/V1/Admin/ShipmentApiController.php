<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShipmentRequest;
use App\Http\Requests\UpdateShipmentRequest;
use App\Http\Resources\Admin\ShipmentResource;
use App\Models\Shipment;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShipmentApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('shipment_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ShipmentResource(Shipment::with(['order', 'fulfilled_by'])->get());
    }

    public function store(StoreShipmentRequest $request)
    {
        $shipment = Shipment::create($request->all());

        return (new ShipmentResource($shipment))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Shipment $shipment)
    {
        abort_if(Gate::denies('shipment_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ShipmentResource($shipment->load(['order', 'fulfilled_by']));
    }

    public function update(UpdateShipmentRequest $request, Shipment $shipment)
    {
        $shipment->update($request->all());

        return (new ShipmentResource($shipment))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Shipment $shipment)
    {
        abort_if(Gate::denies('shipment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $shipment->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
