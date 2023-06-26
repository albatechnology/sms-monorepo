@extends('layouts.admin')
@section('content')
    @can('address_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.interior-designs.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.interiorDesign.title_singular') }}
                </a>
                <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                    {{ trans('global.app_csvImport') }}
                </button>
                @include('csvImport.customModal', ['model' => 'InteriorDesign', 'route' => 'admin.interior-designs.parseCsvImport', 'type' => \App\Enums\Import\ImportBatchType::INTERIOR_DESIGN])
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.interiorDesign.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Address">
                <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th width="10">
                        #
                    </th>
                    <th>
                        {{ trans('cruds.interiorDesign.fields.name') }}
                    </th>
                    <th>
                        {{ trans('cruds.interiorDesign.fields.bum') }}
                    </th>
                    <th>
                        {{ trans('cruds.interiorDesign.fields.sales') }}
                    </th>
                    <th>
                        {{ trans('cruds.interiorDesign.fields.company') }}
                    </th>
                    <th>
                        {{ trans('cruds.interiorDesign.fields.owner') }}
                    </th>
                    <th>
                        {{ trans('cruds.interiorDesign.fields.npwp') }}
                    </th>
                    <th>
                        {{ trans('cruds.interiorDesign.fields.phone') }}
                    </th>
                    <th>
                        {{ trans('cruds.interiorDesign.fields.email') }}
                    </th>
                    <th>
                        Action
                    </th>
                </tr>
                <tr>
                    <td>
                    </td>
                    <td>
                        <input class="search w-100" type="text" strict="true" placeholder="{{ trans('global.search') }}">
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
        $(function () {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

            let dtOverrideGlobals = {
                buttons: dtButtons,
                processing: true,
                serverSide: true,
                retrieve: true,
                aaSorting: [],
                ajax: "{{ route('admin.interior-designs.index') }}",
                columns: [
                    {data: 'placeholder', name: 'placeholder'},
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'bum', name: 'bum'},
                    {data: 'sales', name: 'sales'},
                    {data: 'company', name: 'company'},
                    {data: 'owner', name: 'owner'},
                    {data: 'npwp', name: 'npwp'},
                    {data: 'phone', name: 'phone'},
                    {data: 'email', name: 'email'},
                    {data: 'actions', name: '{{ trans('global.actions') }}'}
                ],
                orderCellsTop: true,
                order: [[1, 'desc']],
                pageLength: 10,
            };
            let table = $('.datatable-Address').DataTable(dtOverrideGlobals);
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
