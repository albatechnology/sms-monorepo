<div class="m-3">
    @can('stock_transfer_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.stock-transfers.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.stockTransfer.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.stockTransfer.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-stockToStockTransfers">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.stockTransfer.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.stockTransfer.fields.stock_from') }}
                            </th>
                            <th>
                                {{ trans('cruds.stockTransfer.fields.stock_to') }}
                            </th>
                            <th>
                                {{ trans('cruds.stockTransfer.fields.requested_by') }}
                            </th>
                            <th>
                                {{ trans('cruds.stockTransfer.fields.approved_by') }}
                            </th>
                            <th>
                                {{ trans('cruds.stockTransfer.fields.amount') }}
                            </th>
                            <th>
                                {{ trans('cruds.stockTransfer.fields.item_from') }}
                            </th>
                            <th>
                                {{ trans('cruds.item.fields.code') }}
                            </th>
                            <th>
                                {{ trans('cruds.stockTransfer.fields.item_to') }}
                            </th>
                            <th>
                                {{ trans('cruds.item.fields.code') }}
                            </th>
                            <th>
                                {{ trans('cruds.stockTransfer.fields.item_code') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stockTransfers as $key => $stockTransfer)
                            <tr data-entry-id="{{ $stockTransfer->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $stockTransfer->id ?? '' }}
                                </td>
                                <td>
                                    {{ $stockTransfer->stock_from->stock ?? '' }}
                                </td>
                                <td>
                                    {{ $stockTransfer->stock_to->stock ?? '' }}
                                </td>
                                <td>
                                    {{ $stockTransfer->requested_by->name ?? '' }}
                                </td>
                                <td>
                                    {{ $stockTransfer->approved_by->name ?? '' }}
                                </td>
                                <td>
                                    {{ $stockTransfer->amount ?? '' }}
                                </td>
                                <td>
                                    {{ $stockTransfer->item_from->name ?? '' }}
                                </td>
                                <td>
                                    {{ $stockTransfer->item_from->code ?? '' }}
                                </td>
                                <td>
                                    {{ $stockTransfer->item_to->name ?? '' }}
                                </td>
                                <td>
                                    {{ $stockTransfer->item_to->code ?? '' }}
                                </td>
                                <td>
                                    {{ $stockTransfer->item_code ?? '' }}
                                </td>
                                <td>
                                    @can('stock_transfer_show')
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.stock-transfers.show', $stockTransfer->id) }}">
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
  let table = $('.datatable-stockToStockTransfers:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection