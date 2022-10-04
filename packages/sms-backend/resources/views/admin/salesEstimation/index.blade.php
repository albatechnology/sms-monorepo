@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('cruds.report.title') }}
    </div>
    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-estimation">
            <thead>
                <tr>
                    <th></th>
                    <th>ID</th>
                    <th>Sales</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Brand</th>
                    <th>Estimated Value</th>
                    <th>Order Value</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
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
                ajax: "{{ route('admin.salesEstimation.index') }}",
                columns: [
                    {
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'user_id',
                        name: 'user.name'
                    },
                    {
                        data: 'customer_id',
                        name: 'customer.first_name'
                    },
                    {
                        data: 'phone',
                        name: 'customer.phone'
                    },
                    {
                        data: 'brand',
                        name: 'brands.name'
                    },
                    {
                        data: 'estimated',
                        name: 'estimated'
                    },
                    {
                        data: 'order_value',
                        name: 'order_value'
                    },
                ],
                orderCellsTop: true,
                order: [
                    [0, 'desc']
                ],
                columnDefs: [
                    { "orderable": false, "targets": [6,7] },
                ],
                pageLength: 25,
            };
            let table = $('.datatable-estimation').DataTable(dtOverrideGlobals);

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
