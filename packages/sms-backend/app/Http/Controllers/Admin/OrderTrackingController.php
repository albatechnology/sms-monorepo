<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderTracking;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class OrderTrackingController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('order_tracking_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = OrderTracking::with(['order'])->select(sprintf('%s.*', (new OrderTracking)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'order_tracking_show';
                $editGate      = 'order_tracking_edit';
                $deleteGate    = 'order_tracking_delete';
                $crudRoutePart = 'order-trackings';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : "";
            });
            $table->addColumn('order_reference', function ($row) {
                return $row->order ? $row->order->reference : '';
            });

            $table->editColumn('type', function ($row) {
                return $row->type ? OrderTracking::TYPE_SELECT[$row->type] : '';
            });
            $table->editColumn('context', function ($row) {
                return $row->context ? $row->context : "";
            });
            $table->editColumn('old_value', function ($row) {
                return $row->old_value ? $row->old_value : "";
            });
            $table->editColumn('new_value', function ($row) {
                return $row->new_value ? $row->new_value : "";
            });

            $table->rawColumns(['actions', 'placeholder', 'order']);

            return $table->make(true);
        }

        $orders = Order::get();

        return view('admin.orderTrackings.index', compact('orders'));
    }

    public function show(OrderTracking $orderTracking)
    {
        abort_if(Gate::denies('order_tracking_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $orderTracking->load('order');

        return view('admin.orderTrackings.show', compact('orderTracking'));
    }
}
