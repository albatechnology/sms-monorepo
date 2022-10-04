<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CurrencyList;
use App\Http\Controllers\Controller;
use App\Models\SupervisorDiscountApprovalLimit;
use App\Models\Currency;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CurrencyController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('currency_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $currencies = Currency::all();

        return view('admin.currencies.index', compact('currencies'));
    }

    public function create()
    {
        abort_if(Gate::denies('currency_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $currencyList = collect(CurrencyList::getInstances())->pluck('description','key')->prepend(trans('global.pleaseSelect'), '');
        return view('admin.currencies.create', ['currencyList' => $currencyList]);
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'main_currency' => 'required|enum_key:' . CurrencyList::class,
            'foreign_currency' => 'required|enum_key:' . CurrencyList::class,
            'value' => 'required|integer',
        ]);

        if (Currency::where('main_currency', $request->main_currency)->where('foreign_currency', $request->foreign_currency)->doesntExist()) {
            Currency::create($validate);
        }
        return redirect()->route('admin.currencies.index')->with('message', 'Data created successfully');
    }

    public function edit(Currency $currency)
    {
        abort_if(Gate::denies('currency_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $currencyList = collect(CurrencyList::getInstances())->pluck('description','key')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.currencies.edit', ['currency' => $currency, 'currencyList' => $currencyList]);
    }

    public function update(Request $request, Currency $currency)
    {
        $validate = $request->validate([
            'main_currency' => 'required|enum_key:' . CurrencyList::class,
            'foreign_currency' => 'required|enum_key:' . CurrencyList::class,
            'value' => 'required|integer',
        ]);
        $currency->update($validate);
        return redirect()->route('admin.currencies.index')->with('message', 'Data updated successfully');
    }

    public function destroy(Currency $currency)
    {
        $currencyId = $currency->id;
        abort_if(Gate::denies('currency_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($currency->delete()) {
            // SupervisorDiscountApprovalLimit::where('currency_id', $currencyId)->delete();
        }

        return back();
    }

    public function massDestroy(Request $request)
    {
        if (Currency::whereIn('id', request('ids'))->delete()) {
            // SupervisorDiscountApprovalLimit::whereIn('currency_id', request('ids'))->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
