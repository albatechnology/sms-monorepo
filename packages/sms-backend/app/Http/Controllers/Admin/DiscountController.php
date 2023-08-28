<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyDiscountRequest;
use App\Http\Requests\StoreDiscountRequest;
use App\Http\Requests\UpdateDiscountRequest;
// use App\Models\Company;
use App\Models\Discount;
use App\Models\Product;
use App\Models\Promo;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class DiscountController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('discount_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Discount::tenanted()->with('promo')->select(sprintf('%s.*', (new Discount)->table));
            $table = DataTables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'discount_show';
                $editGate      = 'discount_edit';
                $deleteGate    = 'discount_delete';
                $crudRoutePart = 'discounts';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });
            $table->editColumn('type', function ($row) {
                return $row->type?->description;
            });
            $table->editColumn('value', function ($row) {
                return $row->type->is(\App\Enums\DiscountType::NOMINAL) ? rupiah($row->value) : $row->value . '%';
            });
            $table->editColumn('scope', function ($row) {
                return $row->scope?->description;
            });
            $table->editColumn('start_time', function ($row) {
                return date('d-m-Y H:i', strtotime($row->promo->start_time));
            });
            $table->editColumn('end_time', function ($row) {
                return date('d-m-Y H:i', strtotime($row->promo->end_time));
            });
            $table->editColumn('is_active', function ($row) {
                return $row->is_active == 1 ? '<i class="fa fa-check text-green"></i>' : '<i class="fa fa-ban text-danger"></i>';
            });
            // $table->addColumn('company', function ($row) {
            //     return $row->company?->name ?? '-';
            // });
            $table->addColumn('product_brand', function ($row) {
                return $row->productBrand?->name ?? '-';
            });
            $table->rawColumns(['actions', 'placeholder', 'is_active']);

            return $table->make(true);
        }

        // $discounts = Discount::tenanted()->with(['company'])->get();

        return view('admin.discounts.index');
    }

    public function create()
    {
        abort_if(Gate::denies('discount_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $promos = Promo::tenanted()->pluck('name', 'id')->prepend('- Select Promo -', null);
        return view('admin.discounts.create', compact('promos'));
    }

    public function store(StoreDiscountRequest $request)
    {
        Discount::create($request->validated());

        return redirect()->route('admin.discounts.index')->with('message', 'Discount created successfully');
    }

    public function edit(Discount $discount)
    {
        abort_if(Gate::denies('discount_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // $companies = Company::tenanted()->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        // $discount->load('company');
        $selectedProducts = [];
        if (isset($discount->product_unit_ids) && !empty($discount->product_unit_ids)) {
            $selectedProducts = Product::tenanted()->whereActive()->whereIn('id', $discount->product_unit_ids)->pluck('name', 'id')->all();
        }

        $promos = Promo::tenanted()->pluck('name', 'id')->prepend('- Select Promo -', null);
        return view('admin.discounts.edit', compact('discount', 'selectedProducts', 'promos'));
    }

    public function update(UpdateDiscountRequest $request, Discount $discount)
    {
        $data = $request->validated();

        if ($request->scope != 1) {
            unset($data['product_unit_ids']);
        }
        $discount->update($data);

        return redirect()->route('admin.discounts.index')->with('message', 'Discount updated successfully');
    }

    public function show(Discount $discount)
    {
        abort_if(Gate::denies('discount_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // $discount->load('company');

        return view('admin.discounts.show', compact('discount'));
    }

    public function destroy(Discount $discount)
    {
        abort_if(Gate::denies('discount_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $discount->delete();

        return back();
    }

    public function massDestroy(MassDestroyDiscountRequest $request)
    {
        Discount::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function getDiscounts($company_id = null)
    {
        $discounts = Discount::tenanted()->select('id', 'name', 'description');
        // if($company_id != null && $company_id > 0) $discounts->where('company_id', $company_id);
        $discounts = $discounts->get();

        return response()->json($discounts);
    }
}
