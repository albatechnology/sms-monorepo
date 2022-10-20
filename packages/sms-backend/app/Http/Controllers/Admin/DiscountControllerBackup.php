<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyDiscountRequest;
use App\Http\Requests\StoreDiscountRequest;
use App\Http\Requests\UpdateDiscountRequest;
use App\Models\Company;
use App\Models\Discount;
use App\Models\Product;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class DiscountControllerBackup extends Controller
{
	public function index()
	{
		abort_if(Gate::denies('discount_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

		$discounts = Discount::tenanted()->with(['company'])->get();

		return view('admin.discounts.index', compact('discounts'));
	}

	public function create()
	{
		abort_if(Gate::denies('discount_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

		$companies = Company::tenanted()->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

		return view('admin.discounts.create', compact('companies'));
	}

	public function store(StoreDiscountRequest $request)
	{
		$data = $request->validated();
		$data['product_unit_ids'] = collect($data['product_unit_ids'])->map(function ($p) {
			return intval($p);
		});
		$data['product_unit_ids'] = json_encode($data['product_unit_ids']);
		$discount = Discount::create($data);

		return redirect()->route('admin.discounts.index');
	}

	public function edit(Discount $discount)
	{
		abort_if(Gate::denies('discount_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

		$companies = Company::tenanted()->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
		$discount->load('company');

		$selectedProducts = [];
		if (isset($discount->product_unit_ids) && $discount->product_unit_ids != null) {
			$selectedProducts = Product::whereActive()->whereIn('id', json_decode($discount->product_unit_ids, true))->pluck('name', 'id')->all();
		}

		return view('admin.discounts.edit', compact('companies', 'discount', 'selectedProducts'));
	}

	public function update(UpdateDiscountRequest $request, Discount $discount)
	{
		$data = $request->validated();
		$data['product_unit_ids'] = collect($data['product_unit_ids'])->map(function ($p) {
			return intval($p);
		});
		$data['product_unit_ids'] = json_encode($data['product_unit_ids']);
		$discount->update($data);

		return redirect()->route('admin.discounts.index');
	}

	public function show(Discount $discount)
	{
		abort_if(Gate::denies('discount_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

		$discount->load('company');

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
}
