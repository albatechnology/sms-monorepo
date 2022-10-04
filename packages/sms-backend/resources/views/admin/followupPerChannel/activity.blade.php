@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('cruds.report.title') }}
    </div>

    <div class="card-body">
        <div class="col-md-12 col-12 row mb-2">
            <div class="col-md-4">
                <input type="date" name="start_date" id="start_date" class="form-control" placeholder="From Date"/>
            </div>
            <div class="col-md-4">
                <input type="date" name="end_date" id="end_date" class="form-control" placeholder="To Date"/>
            </div>
        </div>
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Report">
            <thead>
                <tr>
                    <th width="10"></th>
                    <th>{{ trans('cruds.report.fields.follow_up_datetime') }}</th>
                    <th>{{ trans('cruds.report.fields.customer') }}</th>
                    <th>{{ trans('cruds.report.fields.contact') }}</th>
                    <th>{{ trans('cruds.report.fields.address') }}</th>
                    <th>{{ trans('cruds.report.fields.channel') }}</th>
                    <th>{{ trans('cruds.report.fields.lead') }}</th>
                    <th>{{ trans('cruds.report.fields.follow_up_method') }}</th>
                    <th>{{ trans('cruds.report.fields.status') }}</th>
                    <th>{{ trans('cruds.report.fields.feedback') }}</th>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <x-filter-enum base-enum="{{ \App\Enums\ActivityFollowUpMethod::class }}"></x-filter-enum>
                    <x-filter-enum base-enum="{{ \App\Enums\ActivityStatus::class }}"></x-filter-enum>
                    <td>&nbsp;</td>
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

    $.extend(true, $.fn.dataTable.defaults, {
        orderCellsTop: true,
        order: [[ 1, 'desc' ]],
        pageLength: 10,
    });

  let dtOverrideGlobals = {
        buttons: dtButtons,
        processing: true,
        serverSide: true,
        retrieve: true,
        aaSorting: [],
        ajax: "{{ route('admin.reports.activity') }}",
        ajax: {
            url: "{{ route('admin.reports.activity') }}",
            data: function(d) {
                d.start_date = $('input[name=start_date]').val();
                d.end_date = $('input[name=end_date]').val();
            }
        },
        columns: [
            {data: 'placeholder', name: 'placeholder'},
            {data: 'follow_up_date', name: 'follow_up_date'},
            {data: 'name', name: 'name'},
            {data: 'phone', name: 'phone'},
            {data: 'city', name: 'city'},
            {data: 'channel', name: 'channel'},
            {data: 'lead', name: 'lead'},
            {data: 'follow_up_method', name: 'follow_up_method'},
            {data: 'status', name: 'status'},
            {data: 'feedback', name: 'feedback'},
        ],
        columnDefs:[
            {targets: [0,1], searchable: false},
            {targets: [0], orderable: false}
        ],
        orderCellsTop: true,
        order: [[1, 'desc']],
        pageLength: 10,
    };
    let table = $('.datatable-Report').DataTable(dtOverrideGlobals);
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
    $('input[name=start_date]').change(function() {
        table.ajax.reload();
    });
    $('input[name=end_date]').change(function() {
        table.ajax.reload();
    });
    table.on('column-visibility.dt', function(e, settings, column, state) {
        visibleColumnsIndexes = []
        table.columns(":visible").every(function(colIdx) {
            visibleColumnsIndexes.push(colIdx);
        });
    });
})

</script>
@endsection
