@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.customer.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.customers.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.customer.fields.id') }}
                        </th>
                        <td>
                            {{ $customer->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.customer.fields.title') }}
                        </th>
                        <td>
                            {{ $customer->title?->key ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.customer.fields.first_name') }}
                        </th>
                        <td>
                            {{ $customer->first_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.customer.fields.last_name') }}
                        </th>
                        <td>
                            {{ $customer->last_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.customer.fields.email') }}
                        </th>
                        <td>
                            {{ $customer->email }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.customer.fields.phone') }}
                        </th>
                        <td>
                            {{ $customer->phone }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.customer.fields.description') }}
                        </th>
                        <td>
                            {{ $customer->description }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.customer.fields.default_address_id') }}
                        </th>
                        <td>
                            {{ $customer->default_address_id }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.customers.index') }}">
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
            <a class="nav-link" href="#customer_leads" role="tab" data-toggle="tab">
                {{ trans('cruds.lead.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#customer_addresses" role="tab" data-toggle="tab">
                {{ trans('cruds.address.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#customer_tax_invoices" role="tab" data-toggle="tab">
                {{ trans('cruds.taxInvoice.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="customer_leads">
            @includeIf('admin.customers.relationships.customerLeads', ['leads' => $customer->customerLeads])
        </div>
        <div class="tab-pane" role="tabpanel" id="customer_addresses">
            @includeIf('admin.customers.relationships.customerAddresses', ['addresses' => $customer->customerAddresses])
        </div>
        <div class="tab-pane" role="tabpanel" id="customer_tax_invoices">
            @includeIf('admin.customers.relationships.customerTaxInvoices', ['taxInvoices' => $customer->customerTaxInvoices])
        </div>
    </div>
</div>

@endsection