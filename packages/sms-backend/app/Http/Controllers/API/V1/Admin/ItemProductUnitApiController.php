<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItemProductUnitRequest;
use App\Http\Requests\UpdateItemProductUnitRequest;
use App\Http\Resources\Admin\ItemProductUnitResource;
use App\Models\ItemProductUnit;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ItemProductUnitApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('item_product_unit_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ItemProductUnitResource(ItemProductUnit::with(['product_unit', 'item'])->get());
    }

    public function store(StoreItemProductUnitRequest $request)
    {
        $itemProductUnit = ItemProductUnit::create($request->all());

        return (new ItemProductUnitResource($itemProductUnit))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(ItemProductUnit $itemProductUnit)
    {
        abort_if(Gate::denies('item_product_unit_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ItemProductUnitResource($itemProductUnit->load(['product_unit', 'item']));
    }

    public function update(UpdateItemProductUnitRequest $request, ItemProductUnit $itemProductUnit)
    {
        $itemProductUnit->update($request->all());

        return (new ItemProductUnitResource($itemProductUnit))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(ItemProductUnit $itemProductUnit)
    {
        abort_if(Gate::denies('item_product_unit_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $itemProductUnit->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
