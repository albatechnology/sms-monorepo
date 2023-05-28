@extends('layouts.admin')
@section('content')
    @can('discount_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.discounts.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.discount.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.discount.title_singular') }} {{ trans('global.list') }}
        </div>
        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-discount">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th>
                            {{ trans('cruds.discount.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.discount.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.discount.fields.description') }}
                        </th>
                        <th>
                            {{ trans('cruds.discount.fields.type') }}
                        </th>
                        <th>
                            {{ trans('cruds.discount.fields.activation_code') }}
                        </th>
                        <th>
                            {{ trans('cruds.discount.fields.value') }}
                        </th>
                        <th>
                            {{ trans('cruds.discount.fields.scope') }}
                        </th>
                        <th>
                            {{ trans('cruds.discount.fields.product_category') }}
                        </th>
                        <th>
                            {{ trans('cruds.discount.fields.product_brand') }}
                        </th>
                        <th>
                            {{ trans('cruds.discount.fields.start_time') }}
                        </th>
                        <th>
                            {{ trans('cruds.discount.fields.end_time') }}
                        </th>
                        <th>
                            {{ trans('cruds.discount.fields.is_active') }}
                        </th>
                        {{-- <th>
                            {{ trans('cruds.discount.fields.company') }}
                        </th> --}}
                        <th>&nbsp;</th>
                    </tr>
                    <tr>
                        <td>
                        </td>
                        <x-filter-id></x-filter-id>
                        <td>
                            <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                        </td>
                        <td>
                            <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                        </td>
                        <x-filter-enum base-enum="{{ \App\Enums\DiscountType::class }}"></x-filter-enum>
                        <td>
                            <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                        </td>
                        <td>
                            <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                        </td>
                        <x-filter-enum base-enum="{{ \App\Enums\DiscountScope::class }}"></x-filter-enum>
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
                        {{-- <td></td> --}}
                        <td>
                            <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                        </td>
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
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            @can('discount_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
                let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.discounts.massDestroy') }}",
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
                ajax: "{{ route('admin.discounts.index') }}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'activation_code',
                        name: 'activation_code'
                    },
                    {
                        data: 'value',
                        name: 'value'
                    },
                    {
                        data: 'scope',
                        name: 'scope'
                    },
                    {
                        data: 'product_category',
                        name: 'product_category'
                    },
                    {
                        data: 'product_brand',
                        name: 'product_brand'
                    },
                    {
                        data: 'start_time',
                        name: 'promo.start_time'
                    },
                    {
                        data: 'end_time',
                        name: 'promo.end_time'
                    },
                    {
                        data: 'is_active',
                        name: 'is_active'
                    },
                    // {
                    //     data: 'company',
                    //     name: 'company.name'
                    // },
                    {
                        data: 'actions',
                        name: '{{ trans('global.actions') }}'
                    }
                ],
                orderCellsTop: true,
                order: [
                    [1, 'desc']
                ],
                pageLength: 100,
            };
            let table = $('.datatable-discount').DataTable(dtOverrideGlobals);
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
