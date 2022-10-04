<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\StockTransferStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockTransferRequest;
use App\Http\Resources\Admin\StockTransferResource;
use App\Models\StockTransfer;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StockTransferApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('stock_transfer_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new StockTransferResource(StockTransfer::with(['stock_from', 'stock_to', 'requested_by', 'approved_by', 'item_from', 'item_to'])->get());
    }

    public function store(StoreStockTransferRequest $request)
    {
        $request->status = StockTransferStatus::COMPLETE;
        $stockTransfer = StockTransfer::create($request->all());

        return (new StockTransferResource($stockTransfer))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(StockTransfer $stockTransfer)
    {
        abort_if(Gate::denies('stock_transfer_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new StockTransferResource($stockTransfer->load(['stock_from', 'stock_to', 'requested_by', 'approved_by', 'item_from', 'item_to']));
    }
}
