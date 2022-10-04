@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.address.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.addresses.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.address.fields.id') }}
                        </th>
                        <td>
                            {{ $address->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.address.fields.address_line_1') }}
                        </th>
                        <td>
                            {{ $address->address_line_1 }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.address.fields.address_line_2') }}
                        </th>
                        <td>
                            {{ $address->address_line_2 }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.address.fields.address_line_3') }}
                        </th>
                        <td>
                            {{ $address->address_line_3 }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.address.fields.city') }}
                        </th>
                        <td>
                            {{ $address->city }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.address.fields.country') }}
                        </th>
                        <td>
                            {{ $address->country }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.address.fields.province') }}
                        </th>
                        <td>
                            {{ $address->province }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.address.fields.type') }}
                        </th>
                        <td>
                            {{ App\Enums\AddressType::fromValue($address->type)->description }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.address.fields.phone') }}
                        </th>
                        <td>
                            {{ $address->phone }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.address.fields.customer') }}
                        </th>
                        <td>
                            {{ $address->customer->first_name ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.addresses.index') }}">
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
            <a class="nav-link" href="#address_orders" role="tab" data-toggle="tab">
                {{ trans('cruds.order.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#address_tax_invoices" role="tab" data-toggle="tab">
                {{ trans('cruds.taxInvoice.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="address_orders">
            @includeIf('admin.addresses.relationships.addressOrders', ['orders' => $address->addressOrders])
        </div>
        <div class="tab-pane" role="tabpanel" id="address_tax_invoices">
            @includeIf('admin.addresses.relationships.addressTaxInvoices', ['taxInvoices' => $address->addressTaxInvoices])
        </div>
    </div>
</div>

@endsection