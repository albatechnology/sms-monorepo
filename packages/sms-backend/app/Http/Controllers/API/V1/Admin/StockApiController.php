<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateStockRequest;
use App\Http\Resources\Admin\StockResource;
use App\Models\Stock;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StockApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('stock_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new StockResource(Stock::with(['channel', 'item'])->get());
    }

    public function show(Stock $stock)
    {
        abort_if(Gate::denies('stock_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new StockResource($stock->load(['channel', 'item']));
    }

    public function update(UpdateStockRequest $request, Stock $stock)
    {
        $stock->update($request->all());

        return (new StockResource($stock))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }
}
