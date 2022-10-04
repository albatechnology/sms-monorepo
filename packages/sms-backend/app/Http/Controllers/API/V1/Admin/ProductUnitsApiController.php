<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreProductUnitRequest;
use App\Http\Requests\UpdateProductUnitRequest;
use App\Http\Resources\Admin\ProductUnitResource;
use App\Models\ProductUnit;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductUnitsApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('product_unit_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ProductUnitResource(ProductUnit::with(['product'])->get());
    }

    public function store(StoreProductUnitRequest $request)
    {
        $productUnit = ProductUnit::create($request->all());

        return (new ProductUnitResource($productUnit))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(ProductUnit $productUnit)
    {
        abort_if(Gate::denies('product_unit_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ProductUnitResource($productUnit->load(['product']));
    }

    public function update(UpdateProductUnitRequest $request, ProductUnit $productUnit)
    {
        $productUnit->update($request->all());

        return (new ProductUnitResource($productUnit))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(ProductUnit $productUnit)
    {
        abort_if(Gate::denies('product_unit_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productUnit->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
