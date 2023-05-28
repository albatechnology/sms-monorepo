@extends('layouts.admin')
@section('content')
    @can('product_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.products.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.product.title_singular') }}
                </a>
                <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                    {{ trans('global.app_csvImport') }}
                </button>
                @include('csvImport.customModal', ['model' => 'Product', 'route' => 'admin.products.parseCsvImport', 'type' => \App\Enums\Import\ImportBatchType::PRODUCT])
                {{-- <button class="btn btn-warning" data-toggle="modal" data-target="#mdlGenerateBarcode">Generate Barcode</button> --}}
                {{-- <div class="modal fade" id="mdlGenerateBarcode" tabindex="-1" role="dialog" aria-labelledby="mdlGenerateBarcodeLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="mdlGenerateBarcodeLabel">Generate Barcode</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form class="form-horizontal" method="POST" action="{{ route('admin.products.generateBarcode') }}">
                                    {{ csrf_field() }}
                                    <div class="form-group{{ $errors->has('csv_file') ? ' has-error' : '' }}">
                                        <div class="form-group">
                                            <label class="required">Brand Category</label><br>
                                            <select class="form-control select2" name="brand_category_id" id="brand_category_id" required style="width: 100% !important">
                                                <option value="">- Select -</option>
                                                @foreach(\App\Models\BrandCategory::pluck('name', 'id') as $id => $name)
                                                    <option value="{{ $id }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Product Brand</label><br>
                                            <select class="form-control select2" name="product_brand_ids[]" id="product_brand_ids" multiple disabled style="width: 100% !important" placeholder="Select Brand Category">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Generate</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.product.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Product">
                <thead>
                <tr>
                    <th width="10"></th>
                    <th>
                        {{ trans('cruds.product.fields.id') }}
                    </th>
                    {{-- <th>Barcode</th> --}}
                    <th>
                        {{ trans('cruds.product.fields.name') }}
                    </th>
                    <th>
                        {{ trans('cruds.product.fields.category') }}
                    </th>
                    <th>
                        {{ trans('cruds.product.fields.brand') }}
                    </th>
                    <th>
                        {{ trans('cruds.product.fields.price') }}
                    </th>
                    <th>
                        {{ trans('cruds.product.fields.photo') }}
                    </th>
                    <th>
                        {{ trans('cruds.product.fields.is_active') }}
                    </th>
                    {{-- <th>
                        {{ trans('cruds.product.fields.company') }}
                    </th> --}}
                    <th>
                        &nbsp;
                    </th>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input class="search" type="text" strict="true" placeholder="{{ trans('global.search') }}">
                    </td>
                    {{-- <td></td> --}}
                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>
                    <td>
                        <select class="search">
                            <option value>{{ trans('global.all') }}</option>
                            @foreach($productCategories as $key => $item)
                                <option value="{{ $item->name }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select class="search">
                            <option value>{{ trans('global.all') }}</option>
                            @foreach($productBrands as $key => $item)
                                <option value="{{ $item->name }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
                    {{-- <td>
                        <select class="search">
                            <option value>{{ trans('global.all') }}</option>
                            @foreach($companies as $key => $item)
                                <option value="{{ $item->name }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </td> --}}
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
            @can('product_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.products.massDestroy') }}",
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
                ajax: "{{ route('admin.products.index') }}",
                columns: [
                    {data: 'placeholder', name: 'placeholder'},
                    {data: 'id', name: 'id'},
                    // {data: 'barcode', name: 'barcode', sortable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'category', name: 'productCategory.name'},
                    {data: 'brand', name: 'brand.name'},
                    {data: 'price', name: 'price'},
                    {data: 'photo', name: 'photo', sortable: false, searchable: false},
                    {data: 'is_active', name: 'is_active'},
                    // {data: 'company_name', name: 'company.name'},
                    {data: 'actions', name: '{{ trans('global.actions') }}'}
                ],
                orderCellsTop: true,
                order: [[1, 'desc']],
                pageLength: 10,
            };
            let table = $('.datatable-Product').DataTable(dtOverrideGlobals);
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
            });

            $('#brand_category_id').on('change', function(){
                $.get("{{ url('admin/product-brands/getProductBrand') }}/"+$(this).val(), function(res){
                    var pbHtml = ''
                    $.each(res, function(id, name) {
                        pbHtml += '<option value="'+id+'">'+name+'</option>';
                    });
                    $('#product_brand_ids').html(pbHtml).attr('disabled', false)
                });
            })
        });

    </script>
@endsection
