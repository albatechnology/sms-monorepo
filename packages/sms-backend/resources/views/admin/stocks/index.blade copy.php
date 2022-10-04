@extends('layouts.admin')
@section('content')
    @can('stock_edit')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                    {{ trans('global.app_csvImport') }}
                </button>
                @include('csvImport.customModal', ['model' => 'Stock', 'route' => 'admin.stocks.parseCsvImport', 'type' =>
                \App\Enums\Import\ImportBatchType::STOCK])
                <a href="{{ route('admin.stocks.refreshTotalStock') }}" class="btn btn-warning">Refresh All Total Stock</a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.stock.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Stock">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.stock.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.stock.fields.channel') }}
                        </th>
                        <th>
                            {{ trans('cruds.productUnit.fields.name') }}
                        </th>
                        <th>
                            Ready {{ trans('cruds.stock.fields.stock') }}
                        </th>
                        <th>
                            Indent
                        </th>
                        <th>
                            Outstanding Order
                        </th>
                        <th>
                            Outstanding Shipment
                        </th>
                        <th>
                            Total {{ trans('cruds.stock.fields.stock') }}
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
                                @foreach ($channels as $key => $item)
                                    <option value="{{ $item->name }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input class="search" type="text" strict="true"
                                placeholder="{{ trans('global.search') }}">
                        </td>
                        <td>
                            <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                        </td>
                        <td>
                            <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                        </td>
                        <td>
                            <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                        </td>
                        <td>
                            <input class="search" type="text" placeholder="{{ trans('global.search') }}">
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
                ajax: "{{ route('admin.stocks.getData') }}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'channel_name',
                        name: 'channel.name'
                    },
                    {
                        data: 'product_unit_name',
                        name: 'productUnit.name'
                    },
                    {
                        data: 'stock',
                        name: 'stock'
                    },
                    {
                        data: 'indent',
                        name: 'indent'
                    },
                    {
                        data: 'outstanding_order',
                        name: 'outstanding_order'
                    },
                    {
                        data: 'outstanding_shipment',
                        name: 'outstanding_shipment'
                    },
                    {
                        data: 'total_stock',
                        name: 'total_stock',
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
            let table = $('.datatable-Stock').DataTable(dtOverrideGlobals);
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
