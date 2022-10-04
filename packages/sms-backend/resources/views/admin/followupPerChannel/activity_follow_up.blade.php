@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('cruds.report.activity_follow_up') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Report">
            <thead>
                <tr>
                    <th width="10"></th>
                    <th>{{ trans('cruds.report.fields.follow_up_datetime') }}</th>
                    <th>{{ trans('cruds.report.fields.area') }}</th>
                    <th>{{ trans('cruds.report.fields.sales_name') }}</th>
                    <th>{{ trans('cruds.report.fields.supervisor') }}</th>
                    <th>{{ trans('cruds.report.fields.email') }}</th>
                    <th>{{ trans('cruds.report.fields.channel') }}</th>
                    <th>{{ trans('cruds.report.fields.total_follow_up') }}</th>
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
        ajax: "{{ route('admin.report_activities.activity_follow_up') }}",
        columns: [
            {data: 'placeholder', name: 'placeholder'},
            {data: 'follow_up_date', name: 'follow_up_datetime'},
            {data: 'area', name: 'area.name'},
            {data: 'username', name: 'users.name'},
            {data: 'supervisor', name: 'spv.name'},
            {data: 'user_email', name: 'users.email'},
            {data: 'channel_name', name: 'channels.name'},
            {data: 'total', name: 'total'},
        ],
        orderCellsTop: true,
        order: [[1, 'desc']],
        pageLength: 10,
    };
    let table = $('.datatable-Report').DataTable(dtOverrideGlobals);
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
    table.on('column-visibility.dt', function(e, settings, column, state) {
        visibleColumnsIndexes = []
        table.columns(":visible").every(function(colIdx) {
            visibleColumnsIndexes.push(colIdx);
        });
    });
})

</script>
@endsection
