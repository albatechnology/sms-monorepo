<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LeadStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyCompanyRequest;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class CompanyController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('company_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::tenanted()->with('media')->get();

        return view('admin.companies.index', compact('companies'));
    }

    public function create()
    {
        abort_if(Gate::denies('company_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.companies.create');
    }

    public function store(StoreCompanyRequest $request)
    {
        $company = Company::create($request->validated());

        return redirect()->route('admin.companies.index');
    }

    public function edit(Company $company)
    {
        abort_if(Gate::denies('company_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.companies.edit', compact('company'));
    }

    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $company->name               = $request->get('name');
        $company->company_account_id = $request->get('company_account_id');
        $company->options            = [
            'lead_status_duration_days' => [
                LeadStatus::GREEN  => $request->get('options_lead_status_duration_days_' . LeadStatus::GREEN),
                LeadStatus::YELLOW => $request->get('options_lead_status_duration_days_' . LeadStatus::YELLOW),
                LeadStatus::RED    => $request->get('options_lead_status_duration_days_' . LeadStatus::RED),
            ]
        ];
        $company->save();

        if (!empty($request->input('logo'))) {

            if ($company->logo?->file_name != $request->input('logo')) {
                if (!empty($company->logo)) {
                    $company->logo->delete();
                }

                $company->addMedia(storage_path('tmp/uploads/' . basename($request->input('logo'))))
                    ->toMediaCollection('logo');
            }
        }

        return redirect()->route('admin.companies.index');
    }

    public function show(Company $company)
    {
        abort_if(Gate::denies('company_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $company->load('companyChannels', 'companyProducts', 'companyItems', 'companyProductCategories', 'companyProductTags', 'companyDiscounts', 'companyPromos', 'companyBanners', 'companyPaymentCategories', 'companyPaymentTypes', 'companiesUsers');

        return view('admin.companies.show', compact('company'));
    }

    public function destroy(Company $company)
    {
        abort_if(Gate::denies('company_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $company->delete();

        return back();
    }

    public function massDestroy(MassDestroyCompanyRequest $request)
    {
        Company::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
