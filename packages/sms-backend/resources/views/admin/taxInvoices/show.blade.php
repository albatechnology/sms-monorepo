@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.taxInvoice.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.tax-invoices.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.taxInvoice.fields.id') }}
                        </th>
                        <td>
                            {{ $taxInvoice->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.taxInvoice.fields.customer') }}
                        </th>
                        <td>
                            {{ $taxInvoice->customer->first_name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.taxInvoice.fields.company_name') }}
                        </th>
                        <td>
                            {{ $taxInvoice->company_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.taxInvoice.fields.npwp') }}
                        </th>
                        <td>
                            {{ $taxInvoice->npwp }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.taxInvoice.fields.address') }}
                        </th>
                        <td>
                            {{ $taxInvoice->address->address_line_1 ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.taxInvoice.fields.email') }}
                        </th>
                        <td>
                            {{ $taxInvoice->email }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.tax-invoices.index') }}">
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
            <a class="nav-link" href="#tax_invoice_orders" role="tab" data-toggle="tab">
                {{ trans('cruds.order.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="tax_invoice_orders">
            @includeIf('admin.taxInvoices.relationships.taxInvoiceOrders', ['orders' => $taxInvoice->taxInvoiceOrders])
        </div>
    </div>
</div>

@endsection