<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyItemProductUnitRequest;
use App\Http\Requests\StoreItemProductUnitRequest;
use App\Http\Requests\UpdateItemProductUnitRequest;
use App\Models\Item;
use App\Models\ItemProductUnit;
use App\Models\ProductUnit;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ItemProductUnitController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('item_product_unit_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ItemProductUnit::with(['product_unit', 'item'])->select(sprintf('%s.*', (new ItemProductUnit)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'item_product_unit_show';
                $editGate      = 'item_product_unit_edit';
                $deleteGate    = 'item_product_unit_delete';
                $crudRoutePart = 'item-product-units';

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
            $table->addColumn('product_unit_name', function ($row) {
                return $row->product_unit ? $row->product_unit->name : '';
            });

            $table->addColumn('item_name', function ($row) {
                return $row->item ? $row->item->name : '';
            });

            $table->editColumn('uom', function ($row) {
                return $row->uom ? $row->uom : "";
            });

            $table->rawColumns(['actions', 'placeholder', 'product_unit', 'item']);

            return $table->make(true);
        }

        $product_units = ProductUnit::get();
        $items         = Item::get();

        return view('admin.itemProductUnits.index', compact('product_units', 'items'));
    }

    public function create()
    {
        abort_if(Gate::denies('item_product_unit_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $product_units = ProductUnit::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $items = Item::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.itemProductUnits.create', compact('product_units', 'items'));
    }

    public function store(StoreItemProductUnitRequest $request)
    {
        $itemProductUnit = ItemProductUnit::create($request->all());

        return redirect()->route('admin.item-product-units.index');
    }

    public function edit(ItemProductUnit $itemProductUnit)
    {
        abort_if(Gate::denies('item_product_unit_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $product_units = ProductUnit::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $items = Item::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $itemProductUnit->load('product_unit', 'item');

        return view('admin.itemProductUnits.edit', compact('product_units', 'items', 'itemProductUnit'));
    }

    public function update(UpdateItemProductUnitRequest $request, ItemProductUnit $itemProductUnit)
    {
        $itemProductUnit->update($request->all());

        return redirect()->route('admin.item-product-units.index');
    }

    public function show(ItemProductUnit $itemProductUnit)
    {
        abort_if(Gate::denies('item_product_unit_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $itemProductUnit->load('product_unit', 'item');

        return view('admin.itemProductUnits.show', compact('itemProductUnit'));
    }

    public function destroy(ItemProductUnit $itemProductUnit)
    {
        abort_if(Gate::denies('item_product_unit_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $itemProductUnit->delete();

        return back();
    }

    public function massDestroy(MassDestroyItemProductUnitRequest $request)
    {
        ItemProductUnit::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
