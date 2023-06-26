@extends('layouts.admin')
@section('content')
    @can('payment_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.payments.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.payment.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.payment.title_singular') }} {{ trans('global.list') }}
        </div>
        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Payment">
                <thead>
                <tr>
                    <th width="10"></th>
                    <th>
                        {{ trans('cruds.payment.fields.id') }}
                    </th>
                    <th>
                        {{ trans('cruds.payment.fields.order') }}
                    </th>
                    <th>Uploaded Date</th>
                    <th>Customer</th>
                    <th>
                        {{ trans('cruds.payment.fields.amount') }}
                    </th>
                    <th>
                        {{ trans('cruds.payment.fields.payment_category') }}
                    </th>
                    <th>
                        {{ trans('cruds.payment.fields.payment_type') }}
                    </th>
                    <th>
                        {{ trans('cruds.payment.fields.reference') }}
                    </th>
                    <th>
                        {{ trans('cruds.payment.fields.added_by') }}
                    </th>
                    <th>
                        {{ trans('cruds.payment.fields.approved_by') }}
                    </th>
                    <th>
                        {{ trans('cruds.payment.fields.status') }}
                    </th>
                    <th>
                        &nbsp;
                    </th>
                </tr>
                <tr>
                    <td></td>
                    <x-filter-id></x-filter-id>
                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>
                    <td></td>
                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>
                    <td></td>
                    <td>
                        <select class="search">
                            <option value>{{ trans('global.all') }}</option>
                            @foreach($payment_categories as $key => $item)
                                <option value="{{ $item->name }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
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
                    <x-filter-enum base-enum="{{ \App\Enums\PaymentStatus::class }}"></x-filter-enum>
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
                ajax: "{{ route('admin.payments.index') }}",
                columns: [
                    {data: 'placeholder', name: 'placeholder'},
                    {data: 'id', name: 'id'},
                    {data: 'invoice_number', name: 'order.invoice_number'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'customer', name: 'order.customer.first_name'},
                    {data: 'amount', name: 'amount'},
                    {data: 'payment_category_name', name: 'payment_type.payment_category.name'},
                    {data: 'payment_type_name', name: 'payment_type.name'},
                    {data: 'reference', name: 'reference'},
                    {data: 'added_by_name', name: 'added_by.name'},
                    {data: 'approved_by_name', name: 'approved_by.name'},
                    {data: 'status', name: 'status'},
                    {data: 'actions', name: '{{ trans('global.actions') }}'}
                ],
                orderCellsTop: true,
                order: [[1, 'desc']],
                pageLength: 10,
            };
            let table = $('.datatable-Payment').DataTable(dtOverrideGlobals);
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
