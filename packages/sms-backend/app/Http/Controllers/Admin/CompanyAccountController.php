<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyCompanyAccountRequest;
use App\Http\Requests\StoreCompanyAccountRequest;
use App\Http\Requests\UpdateCompanyAccountRequest;
use App\Models\CompanyAccount;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class CompanyAccountController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('company_account_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $accounts = CompanyAccount::tenanted()->with('company')->get();

        return view('admin.companyAccounts.index', compact('accounts'));
    }

    public function create()
    {
        abort_if(Gate::denies('company_account_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.companyAccounts.create');
    }

    public function store(StoreCompanyAccountRequest $request)
    {
        CompanyAccount::create($request->validated());

        return redirect()->route('admin.company-accounts.index');
    }

    public function edit(CompanyAccount $companyAccount)
    {
        abort_if(Gate::denies('company_account_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companyAccount->checkTenantAccess();

        return view('admin.companyAccounts.edit', compact('companyAccount'));
    }

    public function update(UpdateCompanyAccountRequest $request, CompanyAccount $companyAccount)
    {
        $companyAccount->update($request->validated());

        return redirect()->route('admin.company-accounts.index');
    }

    public function show(CompanyAccount $companyAccount)
    {
        abort_if(Gate::denies('company_account_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companyAccount->checkTenantAccess();

        return view('admin.companyAccounts.show', compact('companyAccount'));
    }

    public function destroy(CompanyAccount $companyAccount)
    {
        abort_if(Gate::denies('company_account_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companyAccount->checkTenantAccess();
        $companyAccount->delete();

        return back();
    }

    public function massDestroy(MassDestroyCompanyAccountRequest $request)
    {
        CompanyAccount::tenanted()->whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
