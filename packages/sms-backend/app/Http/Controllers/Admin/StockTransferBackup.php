<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockTransferRequest;
use App\Models\Item;
use App\Models\Stock;
use App\Models\StockTransfer;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class StockTransferBackup extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('stock_transfer_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = StockTransfer::with(['stock_from', 'stock_to', 'requested_by', 'approved_by', 'item_from', 'item_to'])->select(sprintf('%s.*', (new StockTransfer)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'stock_transfer_show';
                $editGate      = 'stock_transfer_edit';
                $deleteGate    = 'stock_transfer_delete';
                $crudRoutePart = 'stock-transfers';

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
            $table->addColumn('stock_from_stock', function ($row) {
                return $row->stock_from ? $row->stock_from->stock : '';
            });

            $table->addColumn('stock_to_stock', function ($row) {
                return $row->stock_to ? $row->stock_to->stock : '';
            });

            $table->addColumn('requested_by_name', function ($row) {
                return $row->requested_by ? $row->requested_by->name : '';
            });

            $table->addColumn('approved_by_name', function ($row) {
                return $row->approved_by ? $row->approved_by->name : '';
            });

            $table->editColumn('amount', function ($row) {
                return $row->amount ? $row->amount : "";
            });
            $table->addColumn('item_from_name', function ($row) {
                return $row->item_from ? $row->item_from->name : '';
            });

            $table->editColumn('item_from.code', function ($row) {
                return $row->item_from ? (is_string($row->item_from) ? $row->item_from : $row->item_from->code) : '';
            });
            $table->addColumn('item_to_name', function ($row) {
                return $row->item_to ? $row->item_to->name : '';
            });

            $table->editColumn('item_to.code', function ($row) {
                return $row->item_to ? (is_string($row->item_to) ? $row->item_to : $row->item_to->code) : '';
            });
            $table->editColumn('item_code', function ($row) {
                return $row->item_code ? $row->item_code : "";
            });

            $table->rawColumns(['actions', 'placeholder', 'stock_from', 'stock_to', 'requested_by', 'approved_by', 'item_from', 'item_to']);

            return $table->make(true);
        }

        $stocks = Stock::get();
        $users  = User::get();
        $items  = Item::get();

        return view('admin.stockTransfers.index', compact('stocks', 'users', 'items'));
    }

    public function create()
    {
        abort_if(Gate::denies('stock_transfer_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stock_froms = Stock::all()->pluck('stock', 'id')->prepend(trans('global.pleaseSelect'), '');

        $stock_tos = Stock::all()->pluck('stock', 'id')->prepend(trans('global.pleaseSelect'), '');

        $requested_bies = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $approved_bies = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $item_froms = Item::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $item_tos = Item::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.stockTransfers.create', compact('stock_froms', 'stock_tos', 'requested_bies', 'approved_bies', 'item_froms', 'item_tos'));
    }

    public function store(StoreStockTransferRequest $request)
    {
        $stockTransfer = StockTransfer::create($request->validated());

        return redirect()->route('admin.stock-transfers.index');
    }

    public function show(StockTransfer $stockTransfer)
    {
        abort_if(Gate::denies('stock_transfer_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stockTransfer->load('stock_from', 'stock_to', 'requested_by', 'approved_by', 'item_from', 'item_to');

        return view('admin.stockTransfers.show', compact('stockTransfer'));
    }
}
