@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.company.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.companies.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.company.fields.id') }}
                        </th>
                        <td>
                            {{ $company->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.company.fields.name') }}
                        </th>
                        <td>
                            {{ $company->name }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.companies.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        {{ trans('global.relatedData') }}
    </div>
    <ul class="nav nav-tabs" role="tablist" id="relationship-tabs">
        <li class="nav-item">
            <a class="nav-link" href="#company_channels" role="tab" data-toggle="tab">
                {{ trans('cruds.channel.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#company_products" role="tab" data-toggle="tab">
                {{ trans('cruds.product.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#company_items" role="tab" data-toggle="tab">
                {{ trans('cruds.item.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#company_product_categories" role="tab" data-toggle="tab">
                {{ trans('cruds.productCategory.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#company_product_tags" role="tab" data-toggle="tab">
                {{ trans('cruds.productTag.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#company_discounts" role="tab" data-toggle="tab">
                {{ trans('cruds.discount.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#company_promos" role="tab" data-toggle="tab">
                {{ trans('cruds.promo.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#company_banners" role="tab" data-toggle="tab">
                {{ trans('cruds.banner.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#company_payment_categories" role="tab" data-toggle="tab">
                {{ trans('cruds.paymentCategory.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#company_payment_types" role="tab" data-toggle="tab">
                {{ trans('cruds.paymentType.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#companies_users" role="tab" data-toggle="tab">
                {{ trans('cruds.user.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="company_channels">
            @includeIf('admin.companies.relationships.companyChannels', ['channels' => $company->companyChannels])
        </div>
        <div class="tab-pane" role="tabpanel" id="company_products">
            @includeIf('admin.companies.relationships.companyProducts', ['products' => $company->companyProducts])
        </div>
        <div class="tab-pane" role="tabpanel" id="company_items">
            @includeIf('admin.companies.relationships.companyItems', ['items' => $company->companyItems])
        </div>
        <div class="tab-pane" role="tabpanel" id="company_product_categories">
            @includeIf('admin.companies.relationships.companyProductCategories', ['productCategories' => $company->companyProductCategories])
        </div>
        <div class="tab-pane" role="tabpanel" id="company_product_tags">
            @includeIf('admin.companies.relationships.companyProductTags', ['productTags' => $company->companyProductTags])
        </div>
        <div class="tab-pane" role="tabpanel" id="company_discounts">
            @includeIf('admin.companies.relationships.companyDiscounts', ['discounts' => $company->companyDiscounts])
        </div>
        <div class="tab-pane" role="tabpanel" id="company_promos">
            @includeIf('admin.companies.relationships.companyPromos', ['promos' => $company->companyPromos])
        </div>
        <div class="tab-pane" role="tabpanel" id="company_banners">
            @includeIf('admin.companies.relationships.companyBanners', ['banners' => $company->companyBanners])
        </div>
        <div class="tab-pane" role="tabpanel" id="company_payment_categories">
            @includeIf('admin.companies.relationships.companyPaymentCategories', ['paymentCategories' => $company->companyPaymentCategories])
        </div>
        <div class="tab-pane" role="tabpanel" id="company_payment_types">
            @includeIf('admin.companies.relationships.companyPaymentTypes', ['paymentTypes' => $company->companyPaymentTypes])
        </div>
        <div class="tab-pane" role="tabpanel" id="companies_users">
            @includeIf('admin.companies.relationships.companiesUsers', ['users' => $company->companiesUsers])
        </div>
    </div>
</div>

@endsection