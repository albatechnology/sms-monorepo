@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.item.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.items.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.item.fields.id') }}
                        </th>
                        <td>
                            {{ $item->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.item.fields.name') }}
                        </th>
                        <td>
                            {{ $item->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.item.fields.code') }}
                        </th>
                        <td>
                            {{ $item->code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.item.fields.company') }}
                        </th>
                        <td>
                            {{ $item->company->name ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.items.index') }}">
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
            <a class="nav-link" href="#item_item_product_units" role="tab" data-toggle="tab">
                {{ trans('cruds.itemProductUnit.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#item_stocks" role="tab" data-toggle="tab">
                {{ trans('cruds.stock.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#item_from_stock_transfers" role="tab" data-toggle="tab">
                {{ trans('cruds.stockTransfer.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#item_to_stock_transfers" role="tab" data-toggle="tab">
                {{ trans('cruds.stockTransfer.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="item_item_product_units">
            @includeIf('admin.items.relationships.itemItemProductUnits', ['itemProductUnits' => $item->itemItemProductUnits])
        </div>
        <div class="tab-pane" role="tabpanel" id="item_stocks">
            @includeIf('admin.items.relationships.itemStocks', ['stocks' => $item->itemStocks])
        </div>
        <div class="tab-pane" role="tabpanel" id="item_from_stock_transfers">
            @includeIf('admin.items.relationships.itemFromStockTransfers', ['stockTransfers' => $item->itemFromStockTransfers])
        </div>
        <div class="tab-pane" role="tabpanel" id="item_to_stock_transfers">
            @includeIf('admin.items.relationships.itemToStockTransfers', ['stockTransfers' => $item->itemToStockTransfers])
        </div>
    </div>
</div>

@endsection