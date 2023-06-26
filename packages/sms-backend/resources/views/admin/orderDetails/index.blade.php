@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        {{ trans('cruds.orderDetail.title_singular') }} {{ trans('global.list') }}
    </div>
    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-OrderDetail">
            <thead>
                <tr>
                    <th width="10"></th>
                    <th>
                        {{ trans('cruds.orderDetail.fields.id') }}
                    </th>
                    <th>
                        {{ trans('cruds.orderDetail.fields.order') }}
                    </th>
                    <th>
                        {{ trans('cruds.orderDetail.fields.product_unit') }}
                    </th>
                    <th>
                        {{ trans('cruds.orderDetail.fields.quantity') }}
                    </th>
                    <th>
                        {{ trans('cruds.orderDetail.fields.unit_price') }}
                    </th>
                    <th>
                        {{ trans('cruds.orderDetail.fields.total_discount') }}
                    </th>
                    <th>
                        {{ trans('cruds.orderDetail.fields.price') }}
                    </th>
                    <th>
                        {{ trans('cruds.orderDetail.fields.indent') }}
                    </th>
                    <th>
                        {{ trans('cruds.orderDetail.fields.status') }}
                    </th>
                    <th>&nbsp;</th>
                </tr>
                <tr>
                    <td></td>
                    <x-filter-id></x-filter-id>
                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>
                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>
                    <x-filter-id></x-filter-id>
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
                    <x-filter-enum base-enum="{{ \App\Enums\OrderDetailStatus::class }}"></x-filter-enum>
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

  let dtOverrideGlobals = {
    buttons: dtButtons,
    processing: true,
    serverSide: true,
    retrieve: true,
    aaSorting: [],
    ajax: "{{ route('admin.order-details.index') }}",
      columns: [
          {data: 'placeholder', name: 'placeholder'},
          {data: 'id', name: 'id'},
          {data: 'order_invoice_number', name: 'order.invoice_number'},
          {data: 'product_unit_name', name: 'product_unit.name'},
          {data: 'quantity', name: 'quantity'},
          {data: 'unit_price', name: 'unit_price'},
          {data: 'total_discount', name: 'total_discount'},
          {data: 'total_price', name: 'total_price'},
          {data: 'indent', name: 'indent'},
          {data: 'status', name: 'status'},
          {data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 10,
  };
  let table = $('.datatable-OrderDetail').DataTable(dtOverrideGlobals);
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
