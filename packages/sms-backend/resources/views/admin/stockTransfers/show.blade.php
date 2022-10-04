@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.stockTransfer.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.stock-transfers.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.stockTransfer.fields.id') }}
                        </th>
                        <td>
                            {{ $stockTransfer->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.stockTransfer.fields.stock_from') }}
                        </th>
                        <td>
                            {{ $stockTransfer->stock_from->stock ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.stockTransfer.fields.stock_to') }}
                        </th>
                        <td>
                            {{ $stockTransfer->stock_to->stock ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.stockTransfer.fields.requested_by') }}
                        </th>
                        <td>
                            {{ $stockTransfer->requested_by->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.stockTransfer.fields.approved_by') }}
                        </th>
                        <td>
                            {{ $stockTransfer->approved_by->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.stockTransfer.fields.amount') }}
                        </th>
                        <td>
                            {{ $stockTransfer->amount }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.stockTransfer.fields.item_from') }}
                        </th>
                        <td>
                            {{ $stockTransfer->item_from->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.stockTransfer.fields.item_to') }}
                        </th>
                        <td>
                            {{ $stockTransfer->item_to->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.stockTransfer.fields.item_code') }}
                        </th>
                        <td>
                            {{ $stockTransfer->item_code }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.stock-transfers.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection