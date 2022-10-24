@extends('layouts.admin')
@section('content')
    @can('product_unit_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.product-units.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.productUnit.title_singular') }}
                </a>
                <button class="btn btn-info" data-toggle="modal" data-target="#csvExportModal"><i class="fa fa-download"></i> Export CSV</button>
                <div class="modal fade" id="csvExportModal" tabindex="-1" role="dialog" aria-labelledby="csvExportModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="csvExportModalLabel">Export CSV</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class='row'>
                                    <div class='col-md-12'>
                                        <form class="form-horizontal" id="form-export" method="POST" action="{{ route('admin.product-units.export') }}">
                                            {{ csrf_field() }}
                                            <div class="form-group">
                                                <label>Company</label>
                                                <select name="company_id" class="form-control">
                                                    @foreach(\App\Models\Company::pluck('name','id')->all() as $id => $name)
                                                    <option value="{{$id}}">{{$name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Brand</label>
                                                <select name="product_brand_id" class="form-control">
                                                    @foreach(\App\Models\ProductBrand::pluck('name','id')->all() as $id => $name)
                                                    <option value="{{$id}}">{{$name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Start ID</label>
                                                <input name="start_id" type="number" min="1" required class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label>End ID</label>
                                                <input name="end_id" type="number" min="1" required class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-8 col-md-offset-4">
                                                    <button type="submit" class="btn btn-primary" id="btn-export"><i class="fa fa-download"></i> Export CSV</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                    {{ trans('global.app_csvImport') }}
                </button>
                @include('csvImport.customModal', ['model' => 'ProductUnit', 'route' => 'admin.product-units.parseCsvImport', 'type' => \App\Enums\Import\ImportBatchType::PRODUCT_UNIT])
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.productUnit.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-ProductUnit">
                <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.productUnit.fields.id') }}
                    </th>
                    <th>
                        {{ trans('cruds.productUnit.fields.product') }}
                    </th>
                    <th>
                        {{ trans('cruds.productUnit.fields.name') }}
                    </th>
                    <th>
                        {{ trans('cruds.productUnit.fields.price') }}
                    </th>
                    <th>SKU</th>
                    <th>
                        {{ trans('cruds.productUnit.fields.is_active') }}
                    </th>
                    <th>
                        {{ trans('global.category') }}
                    </th>
                    <th>Volume (&#13221;)</th>
                    <th>
                        &nbsp;
                    </th>
                </tr>
                <tr>
                    <td>
                    </td>
                    <td>
                        <input class="search" type="text" strict="true" placeholder="{{ trans('global.search') }}">
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
                    <td></td>
                    <x-filter-enum base-enum="{{ \App\Enums\ProductCategory::class }}"></x-filter-enum>
                    <td></td>
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
        $(function () {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            @can('product_unit_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.product-units.massDestroy') }}",
                className: 'btn-danger',
                action: function (e, dt, node, config) {
                    var ids = $.map(dt.rows({selected: true}).data(), function (entry) {
                        return entry.id
                    });

                    if (ids.length === 0) {
                        alert('{{ trans('global.datatables.zero_selected') }}')

                        return
                    }

                    if (confirm('{{ trans('global.areYouSure') }}')) {
                        $.ajax({
                            headers: {'x-csrf-token': _token},
                            method: 'POST',
                            url: config.url,
                            data: {ids: ids, _method: 'DELETE'}
                        })
                            .done(function () {
                                location.reload()
                            })
                    }
                }
            }
            dtButtons.push(deleteButton)
            @endcan

            let dtOverrideGlobals = {
                buttons: dtButtons,
                processing: true,
                serverSide: true,
                retrieve: true,
                aaSorting: [],
                ajax: "{{ route('admin.product-units.index') }}",
                columns: [
                    {data: 'placeholder', name: 'placeholder'},
                    {data: 'id', name: 'id'},
                    {data: 'product_name', name: 'product.name'},
                    {data: 'name', name: 'name'},
                    {data: 'price', name: 'price'},
                    {data: 'sku', name: 'sku'},
                    {data: 'is_active', name: 'is_active'},
                    {data: 'product_unit_category', name: 'product_unit_category'},
                    {data: 'volume', name: 'volume'},
                    {data: 'actions', name: '{{ trans('global.actions') }}'}
                ],
                orderCellsTop: true,
                order: [[1, 'desc']],
                pageLength: 10,
            };
            let table = $('.datatable-ProductUnit').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function (e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

            let visibleColumnsIndexes = null;
            $('.datatable thead').on('input', '.search', function () {
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
            table.on('column-visibility.dt', function (e, settings, column, state) {
                visibleColumnsIndexes = []
                table.columns(":visible").every(function (colIdx) {
                    visibleColumnsIndexes.push(colIdx);
                });
            })
        });
    </script>
@endsection
