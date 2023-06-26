@extends('layouts.admin')
@section('content')
    @can('product_brand_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.product-brands.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.productBrand.title_singular') }}
                </a>
                <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                    {{ trans('global.app_csvImport') }}
                </button>
                @include('csvImport.customModal', ['model' => 'ProductBrand', 'route' => 'admin.product-brands.parseCsvImport', 'type' => \App\Enums\Import\ImportBatchType::PRODUCT_BRAND])
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.productBrand.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-ProductBrand">
                <thead>
                <tr>
                    <th width="10"> </th>
                    <th>
                        {{ trans('cruds.productBrand.fields.id') }}
                    </th>
                    <th>
                        {{ trans('cruds.productBrand.fields.name') }}
                    </th>
                    <th>Brand Category</th>
                    {{-- <th>Company</th> --}}
                    {{-- <th>
                        {{ trans('cruds.productBrand.fields.hpp_calculation') }}
                    </th> --}}
                    {{-- <th>Currency</th> --}}
                    <th>
                        {{ trans('cruds.productBrand.fields.photo') }}
                    </th>
                    <th>
                        &nbsp;
                    </th>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>
                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>
                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>
                    {{-- <td>
                        <select class="search">
                            <option value>{{ trans('global.all') }}</option>
                            @foreach($companies as $id => $name)
                                <option value="{{ $name }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </td> --}}
                    <td></td>
                    {{-- <td></td> --}}
                    {{-- <td></td> --}}
                    {{-- <td></td> --}}
                    {{-- <td></td> --}}
                    <td></td>
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
            @can('product_brand_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.product-brands.massDestroy') }}",
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
                ajax: "{{ route('admin.product-brands.index') }}",
                columns: [
                    {data: 'placeholder', name: 'placeholder'},
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'brand_category', name: 'brandCategory.name'},
                    // {data: 'company_name', name: 'company.name'},
                    // {data: 'hpp_calculation', name: 'hpp_calculation'},
                    // {data: 'currency_id', name: 'currency_id'},
                    {data: 'photo', name: 'photo', sortable: false, searchable: false},
                    {data: 'actions', name: '{{ trans('global.actions') }}'}
                ],
                orderCellsTop: true,
                order: [[1, 'desc']],
                pageLength: 25,
            };
            let table = $('.datatable-ProductBrand').DataTable(dtOverrideGlobals);
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

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('body').on('click', '.check_active', function() {
            if ($(this).prop('checked')) {
                ajax_activated_data($(this).data('id'), $(this).data('column'), 1)
            } else {
                ajax_activated_data($(this).data('id'), $(this).data('column'), 0)
            }
        });

        function ajax_activated_data(id, column, val) {
            $.post("{{ route('admin.product-brands.ajaxActivationData') }}", {
                id: id,
                column: column,
                val: val
            }, function(res) {
                if (res.success) {
                    toastr.success(res.message);
                } else {
                    toastr.error(res.message);
                }
            }, 'json')
        }
    </script>
@endsection

