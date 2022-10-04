@extends('layouts.admin')
@section('content')
    @can('address_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.addresses.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.address.title_singular') }}
                </a>
                <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                    {{ trans('global.app_csvImport') }}
                </button>
                @include('csvImport.modal', ['model' => 'Address', 'route' => 'admin.addresses.parseCsvImport'])
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.address.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Address">
                <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.address.fields.id') }}
                    </th>
                    <th>
                        {{ trans('cruds.address.fields.address_line_1') }}
                    </th>
                    <th>
                        {{ trans('cruds.address.fields.address_line_2') }}
                    </th>
                    <th>
                        {{ trans('cruds.address.fields.address_line_3') }}
                    </th>
                    <th>
                        {{ trans('cruds.address.fields.city') }}
                    </th>
                    <th>
                        {{ trans('cruds.address.fields.country') }}
                    </th>
                    <th>
                        {{ trans('cruds.address.fields.province') }}
                    </th>
                    <th>
                        {{ trans('cruds.address.fields.type') }}
                    </th>
                    <th>
                        {{ trans('cruds.address.fields.phone') }}
                    </th>
                    <th>
                        {{ trans('cruds.address.fields.customer') }}
                    </th>
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
                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>
                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>
                    <td>
                        <select class="search" strict="true">
                            <option value>{{ trans('global.all') }}</option>
                            @foreach(App\Enums\AddressType::getInstances() as $enum)
                                <option value="{{ $enum->value }}">{{ $enum->description }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>
                    <td>
                        <select class="search">
                            <option value>{{ trans('global.all') }}</option>
                            @foreach($customers as $key => $item)
                                <option value="{{ $item->first_name }}">{{ $item->first_name }}</option>
                            @endforeach
                        </select>
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
                ajax: "{{ route('admin.addresses.index') }}",
                columns: [
                    {data: 'placeholder', name: 'placeholder'},
                    {data: 'id', name: 'id'},
                    {data: 'address_line_1', name: 'address_line_1'},
                    {data: 'address_line_2', name: 'address_line_2'},
                    {data: 'address_line_3', name: 'address_line_3'},
                    {data: 'city', name: 'city'},
                    {data: 'country', name: 'country'},
                    {data: 'province', name: 'province'},
                    {data: 'type', name: 'type'},
                    {data: 'phone', name: 'phone'},
                    {data: 'customer_first_name', name: 'customer.first_name'},
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