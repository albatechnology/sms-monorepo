<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StockTransferStatus;
use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\Company;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockTransfer;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class StockTransferController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('stock_transfer_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = StockTransfer::all();
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->editColumn('company_id', function ($row) {
                return $row->company->name ?? "-";
            });
            $table->editColumn('product_id', function ($row) {
                return $row->product->name ?? "-";
            });
            $table->editColumn('from_channel_id', function ($row) {
                return $row->fromChannel->name ?? "-";
            });
            $table->editColumn('to_channel_id', function ($row) {
                return $row->toChannel->name ?? "-";
            });
            $table->editColumn('amount', function ($row) {
                return $row->amount ?? "-";
            });
            $table->rawColumns(['placeholder']);

            return $table->make(true);
        }

        $channels  = Channel::pluck('name', 'id')->all();

        return view('admin.stockTransfers.index', compact('channels'));
    }

    public function create()
    {
        abort_if(Gate::denies('stock_transfer_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stock_froms = Stock::all()->pluck('stock', 'id')->prepend(trans('global.pleaseSelect'), '');

        $stock_tos = Stock::all()->pluck('stock', 'id')->prepend(trans('global.pleaseSelect'), '');

        $requested_bies = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $approved_bies = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        // $item_froms = Item::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        // $item_tos = Item::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $companies = Company::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.stockTransfers.create', compact('companies', 'stock_froms', 'stock_tos', 'requested_bies', 'approved_bies'));
    }

    public function store(Request $request)
    {
        $cut_indent = $request->cut_indent;
        $validated = $request->validate([
            'company_id' => 'required|numeric',
            'from_channel_id' => 'required|numeric',
            'product_id' => 'required|numeric',
            'amount' => 'required|numeric',
            'to_channel_id' => 'required|numeric|different:from_channel_id',
        ]);

        $stock = Stock::where('channel_id', $validated['from_channel_id'])->where('company_id', $validated['company_id'])->where('product_id', $validated['product_id'])->first();

        if ($validated['amount'] > $stock->stock) {
            return redirect()->back()->withInput()->with('message', 'Not enough stock');
        }

        DB::transaction(function () use ($validated, $stock, $cut_indent) {
            $validated['status'] = StockTransferStatus::COMPLETE;

            StockTransfer::create($validated);

            $stock->deductStockWithRefreshTotalStock($validated['amount']);

            $transferTo = Stock::where('channel_id', $validated['to_channel_id'])->where('company_id', $validated['company_id'])->where('product_id', $validated['product_id'])->first();
            $transferTo->addStockNew($validated['amount'], $cut_indent);
        });
        return redirect()->route('admin.stock-transfers.index');
    }

    public function show(StockTransfer $stockTransfer)
    {
        abort_if(Gate::denies('stock_transfer_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stockTransfer->load('stock_from', 'stock_to', 'requested_by', 'approved_by', 'item_from', 'item_to');

        return view('admin.stockTransfers.show', compact('stockTransfer'));
    }

    public function getChannels(Request $request, $companyId)
    {
        $html = "";
        if ($request->ajax()) {
            $channels = Channel::where('company_id', $companyId)->get()->pluck("name", "id");
            foreach ($channels as $id => $name) {
                $html .= '<option value="' . $id . '">' . $name . '</option>';
            }
        }
        return $html;
    }

    public function getProducts(Request $request)
    {
        $data = [];
        if ($request->has('q')) {
            $search = $request->q;
            $data = Product::whereActive()->select("id", "name")->where('company_id', $request->company_id)->where('name', 'LIKE', "%$search%")->get();
        }
        return response()->json($data);
    }

    public function detailStock($companyId, $fromChannelId, $toChannelId, $productId)
    {
        $stocks = Stock::where('company_id', $companyId)->whereIn('channel_id', [$fromChannelId, $toChannelId])->where('product_id', $productId)->select('channel_id', 'stock')->get();
        $data = [];
        foreach ($stocks as $stock) {
            // array_push($data, [
            //     $stock->channel_id => $stock->stock
            // ]);
            $data[$stock->channel_id] = $stock->stock;
        }
        return response()->json($data);
    }
}
