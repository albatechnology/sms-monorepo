@extends('layouts.admin')
@section('content')
    @can('stock_transfer_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.stock-transfers.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.stockTransfer.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.stockTransfer.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-StockTransfer">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.stockTransfer.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.stockTransfer.fields.stock_from') }}
                        </th>
                        <th>
                            {{ trans('cruds.stockTransfer.fields.stock_to') }}
                        </th>
                        <th>
                            {{ trans('cruds.stockTransfer.fields.requested_by') }}
                        </th>
                        <th>
                            {{ trans('cruds.stockTransfer.fields.approved_by') }}
                        </th>
                        <th>
                            {{ trans('cruds.stockTransfer.fields.amount') }}
                        </th>
                        <th>
                            {{ trans('cruds.stockTransfer.fields.item_from') }}
                        </th>
                        <th>
                            {{ trans('cruds.item.fields.code') }}
                        </th>
                        <th>
                            {{ trans('cruds.stockTransfer.fields.item_to') }}
                        </th>
                        <th>
                            {{ trans('cruds.item.fields.code') }}
                        </th>
                        <th>
                            {{ trans('cruds.stockTransfer.fields.item_code') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    <tr>
                        <td>
                        </td>
                        <td>
                            <input class="search" type="text" strict="true"
                                placeholder="{{ trans('global.search') }}">
                        </td>
                        <td>
                            <select class="search">
                                <option value>{{ trans('global.all') }}</option>
                                @foreach ($stocks as $key => $item)
                                    <option value="{{ $item->stock }}">{{ $item->stock }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="search">
                                <option value>{{ trans('global.all') }}</option>
                                @foreach ($stocks as $key => $item)
                                    <option value="{{ $item->stock }}">{{ $item->stock }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="search">
                                <option value>{{ trans('global.all') }}</option>
                                @foreach ($users as $key => $item)
                                    <option value="{{ $item->name }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="search">
                                <option value>{{ trans('global.all') }}</option>
                                @foreach ($users as $key => $item)
                                    <option value="{{ $item->name }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                        </td>
                        <td>
                            <select class="search">
                                <option value>{{ trans('global.all') }}</option>
                                @foreach ($items as $key => $item)
                                    <option value="{{ $item->name }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                        </td>
                        <td>
                            <select class="search">
                                <option value>{{ trans('global.all') }}</option>
                                @foreach ($items as $key => $item)
                                    <option value="{{ $item->name }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                        </td>
                        <td>
                            <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                        </td>
                        <td>
                        </td>
                    </tr>
                </thead>
            </table>
        </div>
    </div>



@endsection
@section('scripts')
    @parent
    <script>
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

            let dtOverrideGlobals = {
                buttons: dtButtons,
                processing: true,
                serverSide: true,
                retrieve: true,
                aaSorting: [],
                ajax: "{{ route('admin.stock-transfers.index') }}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'stock_from_stock',
                        name: 'stock_from.stock'
                    },
                    {
                        data: 'stock_to_stock',
                        name: 'stock_to.stock'
                    },
                    {
                        data: 'requested_by_name',
                        name: 'requested_by.name'
                    },
                    {
                        data: 'approved_by_name',
                        name: 'approved_by.name'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'item_from_name',
                        name: 'item_from.name'
                    },
                    {
                        data: 'item_from.code',
                        name: 'item_from.code'
                    },
                    {
                        data: 'item_to_name',
                        name: 'item_to.name'
                    },
                    {
                        data: 'item_to.code',
                        name: 'item_to.code'
                    },
                    {
                        data: 'item_code',
                        name: 'item_code'
                    },
                    {
                        data: 'actions',
                        name: '{{ trans('global.actions') }}'
                    }
                ],
                orderCellsTop: true,
                order: [
                    [1, 'desc']
                ],
                pageLength: 10,
            };
            let table = $('.datatable-StockTransfer').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

            let visibleColumnsIndexes = null;
            $('.datatable thead').on('input', '.search', function() {
                let strict = $(this).attr('strict') || false
                let value = strict && this.value ? "^" + this.value + "$" : this.value

                let index = $(this).parent().index()
                if (visibleColumnsIndexes !== null) {
                    index = visibleColumnsIndexes[index]
                }

                table
                    .column(index)
                    .search(value, strict)
                    .draw()
            });
            table.on('column-visibility.dt', function(e, settings, column, state) {
                visibleColumnsIndexes = []
                table.columns(":visible").every(function(colIdx) {
                    visibleColumnsIndexes.push(colIdx);
                });
            })
        });
    </script>
@endsection
