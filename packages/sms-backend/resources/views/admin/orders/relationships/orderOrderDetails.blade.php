<div class="m-3">

    <div class="card">
        <div class="card-header">
            {{ trans('cruds.orderDetail.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-orderOrderDetails">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.orderDetail.fields.id') }}
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
                                {{ trans('cruds.orderDetail.fields.total_price') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($orderDetails as $key => $orderDetail)
                        <tr data-entry-id="{{ $orderDetail->id }}">
                            <td>

                            </td>
                                <td>
                                    {{ $orderDetail->id ?? '' }}
                                </td>
                            <td>
                                {{ $orderDetail->product_unit->name ?? '' }}
                            </td>
                            <td>
                                {{ $orderDetail->quantity ?? '' }}
                            </td>
                            <td>
                                {{ rupiah($orderDetail->unit_price) ?? '' }}
                            </td>
                            <td>
                                {{ rupiah($orderDetail->total_discount) ?? '' }}
                            </td>
                            <td>
                                {{ rupiah($orderDetail->total_price) ?? '' }}
                            </td>
                            <td>
                                @can('order_detail_show')
                                    <a class="btn btn-xs btn-primary"
                                       href="{{ route('admin.order-details.show', $orderDetail->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan



                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
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
  let table = $('.datatable-orderOrderDetails:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection