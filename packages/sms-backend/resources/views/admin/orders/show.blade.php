@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.order.title') }}
        </div>
        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.orders.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.order.fields.invoice_number') }}
                            </th>
                            <td>
                                {{ $order->invoice_number }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.order.fields.order_date') }}
                            </th>
                            <td>
                                {{ date('d-m-Y H:i:s', strtotime($order->created_at)) }}
                            </td>
                        </tr>
                        <x-show-row :model="$order" key="deal_at"></x-show-row>
                        <tr>
                            <th>
                                {{ trans('cruds.order.fields.expected_delivery_date') }}
                            </th>
                            <td>
                                {{ $order->expected_shipping_datetime ? date('d-m-Y H:i:s', strtotime($order->expected_shipping_datetime)) : '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.order.fields.quotation_valid_until_datetime') }}
                            </th>
                            <td>
                                {{ $order->quotation_valid_until_datetime ? date('d-m-Y H:i:s', strtotime($order->quotation_valid_until_datetime)) : '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.order.fields.user') }}
                            </th>
                            <td>
                                {{ $order->user?->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.order.fields.customer') }}
                            </th>
                            <td>
                                {{ $order->customer?->full_name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.order.fields.channel') }}
                            </th>
                            <td>
                                {{ $order->channel?->name ?? '' }}
                            </td>
                        </tr>
                        {{-- <tr>
                            <th>Interior Design</th>
                            <td>
                                {{ $order->interiorDesign?->name ?? '' }}
                            </td>
                        </tr> --}}
                        <tr>
                            <th>
                                {{ trans('cruds.order.fields.reference') }}
                            </th>
                            <td>
                                {{ $order->reference }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.order.fields.shipping_address') }}
                            </th>
                            <td>
                                {{ $order->shipping_address?->toString() ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.order.fields.billing_address') }}
                            </th>
                            <td>
                                {{ $order->billing_address?->toString() ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.order.fields.status') }}
                            </th>
                            <td>
                                {{ $order->status->description ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.order.fields.payment_status') }}
                            </th>
                            <td>
                                {{ $order->payment_status?->description ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.order.fields.shipment_status') }}
                            </th>
                            <td>
                                {{ $order->shipment_status?->description ?? '' }}
                            </td>
                        </tr>
                        {{-- <tr>
                        <th>
                            {{ trans('cruds.order.fields.tax_invoice_sent') }}
                        </th>
                        <td>
                            <input type="checkbox" disabled="disabled" {{ $order->tax_invoice_sent ? 'checked' : '' }}>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.tax_invoice') }}
                        </th>
                        <td>
                            {{ $order->tax_invoice->company_name ?? '' }}
                        </td>
                    </tr> --}}
                        <x-show-row :model="$order" key="total_discount"
                            type="{{ \App\View\Components\ShowRow::TYPE_PRICE }}"></x-show-row>
                        <x-show-row :model="$order" key="shipping_fee"
                            type="{{ \App\View\Components\ShowRow::TYPE_PRICE }}"></x-show-row>
                        <x-show-row :model="$order" key="packing_fee"
                            type="{{ \App\View\Components\ShowRow::TYPE_PRICE }}"></x-show-row>
                        <x-show-row :model="$order" key="additional_discount"
                            type="{{ \App\View\Components\ShowRow::TYPE_PRICE }}"></x-show-row>
                        <tr>
                            <th>
                                {{ trans('cruds.order.fields.price') }}
                            </th>
                            <td>
                                {{ rupiah($order->total_price) }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.order.fields.outstanding') }}
                            </th>
                            <td>
                                {{ rupiah($order->total_price - $total_amount) }}
                            </td>
                        </tr>
                        <tr>
                            <th>Discount Used</th>
                            <td>
                                @if ($order->discount)
                                    {{ $order->discount?->name ?? '' }}
                                @else
                                    <p class="text-muted">No discount used</p>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.orders.index') }}">
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
            {{-- <li class="nav-item">
                <a class="nav-link" href="#order_order_trackings" role="tab" data-toggle="tab">
                    {{ trans('cruds.orderTracking.title') }}
                </a>
            </li> --}}
            <li class="nav-item">
                <a class="nav-link active" href="#order_order_details" role="tab" data-toggle="tab"
                    aria-selected="true">
                    {{ trans('cruds.orderDetail.title') }}
                </a>
            </li>
            {{-- <li class="nav-item">
                <a class="nav-link" href="#order_shipments" role="tab" data-toggle="tab">
                    {{ trans('cruds.shipment.title') }}
                </a>
            </li> --}}
            <li class="nav-item">
                <a class="nav-link" href="#order_payments" role="tab" data-toggle="tab">
                    {{ trans('cruds.payment.title') }}
                </a>
            </li>
            {{-- <li class="nav-item">
                <a class="nav-link" href="#product_units" role="tab" data-toggle="tab">
                    Product Units
                </a>
            </li> --}}
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" role="tabpanel" id="order_order_details">
                @includeIf('admin.orders.relationships.orderOrderDetails', [
                    'orderDetails' => $order->orderOrderDetails,
                ])
            </div>
            {{-- <div class="tab-pane" role="tabpanel" id="order_shipments">
                @includeIf('admin.orders.relationships.orderShipments', ['shipments' => $order->orderShipments])
            </div> --}}
            <div class="tab-pane" role="tabpanel" id="order_payments">
                @includeIf('admin.orders.relationships.orderPayments', ['payments' => $order->orderPayments])
            </div>
            {{-- <div class="tab-pane" role="tabpanel" id="product_units">
                @if ($order->cartDemand)
                    @includeIf('admin.orders.relationships.insertProductUnits', ['company' => ['id' => $order->company_id, 'name' => $order->company->name], 'cartDemand' => $order->cartDemand])
                @else
                    <div class="m-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="alert alert-info">Data is empty</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div> --}}
        </div>
    </div>
@endsection
