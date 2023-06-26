<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderDetailStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\UpdateOrderDetailRequest;
use App\Models\OrderDetail;
use App\Services\HelperService;
use App\Services\StockService;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class OrderDetailController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('order_detail_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = OrderDetail::with(['product', 'order'])->select(sprintf('%s.*', (new OrderDetail)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'order_detail_show';
                $editGate      = 'order_detail_edit';
                $crudRoutePart = 'order-details';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ?? "";
            });
            $table->addColumn('product_name', function ($row) {
                return $row->product?->name ?? '';
            });

            $table->addColumn('order_invoice_number', function ($row) {
                return $row->order?->invoice_number ?? '';
            });

            $table->editColumn('quantity', function ($row) {
                return $row->quantity ?? "";
            });
            $table->editColumn('unit_price', function ($row) {
                return HelperService::formatRupiah($row->unit_price) ?? "";
            });
            $table->editColumn('total_discount', function ($row) {
                return HelperService::formatRupiah($row->total_discount) ?? "";
            });
            $table->editColumn('total_price', function ($row) {
                return HelperService::formatRupiah($row->total_price) ?? "";
            });
            $table->addColumn('indent', function ($row) {
                return $row->quantity - $row->quantity_fulfilled;
            });
            $table->editColumn('status', function ($row) {
                return $row->status?->description ?? "";
            });

            $table->rawColumns(['actions', 'placeholder', 'product', 'order']);

            return $table->make(true);
        }

        return view('admin.orderDetails.index');
    }

    public function fulfil(OrderDetail $orderDetail)
    {
        StockService::fulfillOrderDetail($orderDetail);
        return redirect()->back();
    }

    public function edit(OrderDetail $orderDetail)
    {
        abort_if(Gate::denies('order_detail_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $status = OrderDetailStatus::getInstances();
        $orderDetail->load('order');


        return view('admin.orderDetails.edit', compact('status', 'orderDetail'));
    }

    public function update(UpdateOrderDetailRequest $request, OrderDetail $orderDetail)
    {
        $orderDetail->update($request->validated());

        return redirect()->route('admin.order-details.index');
    }

    public function show(OrderDetail $orderDetail)
    {
        abort_if(Gate::denies('order_detail_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $orderDetail->load('product', 'order');

        return view('admin.orderDetails.show', compact('orderDetail'));
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('order_detail_create') && Gate::denies('order_detail_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new OrderDetail();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    public function destroy(OrderDetail $orderDetail)
    {
        abort_if(Gate::denies('order_detail_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $orderDetail->delete();

        return back();
    }
}
