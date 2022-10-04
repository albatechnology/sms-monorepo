<div class="m-3">

    <div class="card">
        <div class="card-header">
            {{ trans('cruds.stock.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-itemStocks">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.stock.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.stock.fields.channel') }}
                            </th>
                            <th>
                                {{ trans('cruds.stock.fields.stock') }}
                            </th>
                            <th>
                                {{ trans('cruds.stock.fields.item') }}
                            </th>
                            <th>
                                {{ trans('cruds.item.fields.code') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stocks as $key => $stock)
                            <tr data-entry-id="{{ $stock->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $stock->id ?? '' }}
                                </td>
                                <td>
                                    {{ $stock->channel->name ?? '' }}
                                </td>
                                <td>
                                    {{ $stock->stock ?? '' }}
                                </td>
                                <td>
                                    {{ $stock->item->name ?? '' }}
                                </td>
                                <td>
                                    {{ $stock->item->code ?? '' }}
                                </td>
                                <td>
                                    @can('stock_show')
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.stocks.show', $stock->id) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan

                                    @can('stock_edit')
                                        <a class="btn btn-xs btn-info" href="{{ route('admin.stocks.edit', $stock->id) }}">
                                            {{ trans('global.edit') }}
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
  let table = $('.datatable-itemStocks:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection