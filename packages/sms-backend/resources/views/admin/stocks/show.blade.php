@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.stock.title') }}
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.stocks.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.stock.fields.id') }}
                            </th>
                            <td>
                                {{ $stock->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.stock.fields.channel') }}
                            </th>
                            <td>
                                {{ $stock->channel->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.stock.fields.stock') }}
                            </th>
                            <td>
                                {{ $stock->stock }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Outstanding Order
                            </th>
                            <td>
                                {{ $outstandingOrder }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Outstanding Shipment
                            </th>
                            <td>
                                {{ $outstandingShipment }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.stocks.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if ($outstandingShipment > 0)
        <div class="card">
            <div class="card-header">Outstanding Shipment</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Created At</th>
                                <th>Invoice Number</th>
                                <th>Sales</th>
                                <th>Deal At</th>
                                <th>Expected Delivery At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stock->outstandingShipmentDetail($stock->company_id, $stock->channel_id, $stock->product_unit_id) as $order)
                                <tr>
                                    <td>{{ $order->created_at }}</td>
                                    <td>{{ $order->invoice_number }}</td>
                                    <td>{{ $order->user->name }}</td>
                                    <td>{{ $order->deal_at ?? '-' }}</td>
                                    <td>{{ $order->expected_shipping_datetime ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
    {{-- <div class="card">
        <div class="card-header">
            {{ trans('global.relatedData') }}
        </div>
        <ul class="nav nav-tabs" role="tablist" id="relationship-tabs">
            <li class="nav-item">
                <a class="nav-link" href="#stock_from_stock_transfers" role="tab" data-toggle="tab">
                    {{ trans('cruds.stockTransfer.title') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#stock_to_stock_transfers" role="tab" data-toggle="tab">
                    {{ trans('cruds.stockTransfer.title') }}
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane" role="tabpanel" id="stock_from_stock_transfers">
                @includeIf('admin.stocks.relationships.stockFromStockTransfers', ['stockTransfers' => $stock->stockFromStockTransfers])
            </div>
            <div class="tab-pane" role="tabpanel" id="stock_to_stock_transfers">
                @includeIf('admin.stocks.relationships.stockToStockTransfers', ['stockTransfers' => $stock->stockToStockTransfers])
            </div>
        </div>
    </div> --}}
@endsection
