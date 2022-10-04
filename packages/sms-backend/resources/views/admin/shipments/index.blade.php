@extends('layouts.admin')
@section('content')
@can('shipment_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.shipments.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.shipment.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.shipment.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Shipment">
            <thead>
                <tr>
                    <th width="10">&nbsp;</th>
                    <th>
                        {{ trans('cruds.shipment.fields.id') }}
                    </th>
                    <th>
                        {{ trans('cruds.shipment.fields.order') }}
                    </th>
                    <th>
                        {{ trans('cruds.shipment.fields.fulfilled_by') }}
                    </th>
                    <th>
                        {{ trans('cruds.shipment.fields.status') }}
                    </th>
                    <th>
                        {{ trans('cruds.shipment.fields.note') }}
                    </th>
                    <th>
                        {{ trans('cruds.shipment.fields.reference') }}
                    </th>
                    <th>
                        {{ trans('cruds.shipment.fields.arrived_at') }}
                    </th>
                    <th>&nbsp;</th>
                </tr>
                <tr>
                    <td>
                    </td>
                    <td>
                        <input class="search" type="text" strict="true" placeholder="{{ trans('global.search') }}">
                    </td>
                    <td>
                        <input class="search" type="text" strict="true" placeholder="{{ trans('global.search') }}">
                    </td>
                    <td>
                        <input class="search" type="text" strict="true" placeholder="{{ trans('global.search') }}">
                    </td>
                    <x-filter-enum base-enum="{{ \App\Enums\ShipmentStatus::class }}"></x-filter-enum>
                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>
                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>
                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>
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
@can('shipment_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.shipments.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).data(), function (entry) {
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
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
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
    ajax: "{{ route('admin.shipments.index') }}",
    columns: [
        { data: 'placeholder', name: 'placeholder' },
        { data: 'id', name: 'id' },
        { data: 'order_reference', name: 'order.invoice_number' },
        { data: 'fulfilled_by_name', name: 'fulfilled_by.name' },
        { data: 'status', name: 'status' },
        { data: 'note', name: 'note' },
        { data: 'reference', name: 'reference' },
        { data: 'arrived_at', name: 'arrived_at' },
        { data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 10,
  };
  let table = $('.datatable-Shipment').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
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
table.on('column-visibility.dt', function(e, settings, column, state) {
      visibleColumnsIndexes = []
      table.columns(":visible").every(function(colIdx) {
          visibleColumnsIndexes.push(colIdx);
      });
  })
});

</script>
@endsection