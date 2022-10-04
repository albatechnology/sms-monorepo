<div class="m-3">

    <div class="card">
        <div class="card-header">
            {{ trans('cruds.orderTracking.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-orderOrderTrackings">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.orderTracking.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.orderTracking.fields.order') }}
                            </th>
                            <th>
                                {{ trans('cruds.orderTracking.fields.type') }}
                            </th>
                            <th>
                                {{ trans('cruds.orderTracking.fields.context') }}
                            </th>
                            <th>
                                {{ trans('cruds.orderTracking.fields.old_value') }}
                            </th>
                            <th>
                                {{ trans('cruds.orderTracking.fields.new_value') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orderTrackings as $key => $orderTracking)
                            <tr data-entry-id="{{ $orderTracking->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $orderTracking->id ?? '' }}
                                </td>
                                <td>
                                    {{ $orderTracking->order->reference ?? '' }}
                                </td>
                                <td>
                                    {{ App\Models\OrderTracking::TYPE_SELECT[$orderTracking->type] ?? '' }}
                                </td>
                                <td>
                                    {{ $orderTracking->context ?? '' }}
                                </td>
                                <td>
                                    {{ $orderTracking->old_value ?? '' }}
                                </td>
                                <td>
                                    {{ $orderTracking->new_value ?? '' }}
                                </td>
                                <td>
                                    @can('order_tracking_show')
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.order-trackings.show', $orderTracking->id) }}">
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
  let table = $('.datatable-orderOrderTrackings:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection