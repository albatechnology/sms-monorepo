<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ShipmentStatus;
use App\Events\ShipmentUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyShipmentRequest;
use App\Http\Requests\StoreShipmentRequest;
use App\Http\Requests\UpdateShipmentRequest;
use App\Models\OrderDetailShipment;
use App\Models\Shipment;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ShipmentController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('shipment_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Shipment::with(['order', 'fulfilled_by'])->select(sprintf('%s.*', (new Shipment)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'shipment_show';
                $editGate      = 'shipment_edit';
                $deleteGate    = 'shipment_delete';
                $crudRoutePart = 'shipments';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : "";
            });
            $table->addColumn('order_reference', function ($row) {
                return $row->order ? $row->order->invoice_number : '';
            });

            $table->addColumn('fulfilled_by_name', function ($row) {
                return $row->fulfilled_by ? $row->fulfilled_by->name : '';
            });

            $table->editColumn('status', function ($row) {
                return $row->status?->description ?? '';
            });
            $table->editColumn('note', function ($row) {
                return $row->note ? $row->note : "";
            });
            $table->editColumn('reference', function ($row) {
                return $row->reference ? $row->reference : "";
            });

            $table->rawColumns(['actions', 'placeholder', 'order', 'fulfilled_by']);

            return $table->make(true);
        }

        return view('admin.shipments.index');
    }

    public function create()
    {
        abort_if(Gate::denies('shipment_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.shipments.create');
    }

    public function store(StoreShipmentRequest $request)
    {
        $shipment                  = new Shipment();
        $originalStatus = $shipment->getOriginal('status');
        $shipment->order_id        = $request->get('order_id');
        $shipment->status          = $request->get('status');
        $shipment->reference       = $request->get('reference');
        $shipment->note            = $request->get('note');
        $shipment->fulfilled_by_id = auth()->user()->id;
        $shipment->save();

        collect($request->get('detail'))->each(function ($data) use ($shipment, $originalStatus) {
            OrderDetailShipment::create([
                'shipment_id'     => $shipment->id,
                'order_detail_id' => $data['id'],
                'quantity'        => $data['quantity'],
            ]);
            $shipment->cutStock($data, $originalStatus);
        });

        ShipmentUpdated::dispatch($shipment);

        return redirect()->route('admin.shipments.index');
    }

    public function edit(Shipment $shipment)
    {
        abort_if(Gate::denies('shipment_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $shipment->loadMissing('order', 'orderDetails');

        return view('admin.shipments.edit', compact('shipment'));
    }

    public function update(UpdateShipmentRequest $request, Shipment $shipment)
    {
        $originalStatus = $shipment->getOriginal('status')->value;
        $shipment->update($request->validated());
        if ($orderDetailShipment = $shipment->orderDetailShipment) {
            foreach ($orderDetailShipment as $detail) {
                $data = ['id' => $detail->order_detail_id, 'quantity' => $detail->quantity];
                $shipment->cutStock($data, $originalStatus);
            }
        }
        return redirect()->route('admin.shipments.index');
    }

    public function show(Shipment $shipment)
    {
        abort_if(Gate::denies('shipment_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $shipment->load('order', 'fulfilled_by');

        return view('admin.shipments.show', compact('shipment'));
    }

    public function destroy(Shipment $shipment)
    {
        abort_if(Gate::denies('shipment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $shipment->delete();

        return back();
    }

    public function massDestroy(MassDestroyShipmentRequest $request)
    {
        Shipment::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
