<div class="m-3">
    @can('item_product_unit_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.item-product-units.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.itemProductUnit.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.itemProductUnit.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-productUnitItemProductUnits">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.itemProductUnit.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.itemProductUnit.fields.product_unit') }}
                            </th>
                            <th>
                                {{ trans('cruds.itemProductUnit.fields.item') }}
                            </th>
                            <th>
                                {{ trans('cruds.itemProductUnit.fields.uom') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($itemProductUnits as $key => $itemProductUnit)
                            <tr data-entry-id="{{ $itemProductUnit->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $itemProductUnit->id ?? '' }}
                                </td>
                                <td>
                                    {{ $itemProductUnit->product_unit->name ?? '' }}
                                </td>
                                <td>
                                    {{ $itemProductUnit->item->name ?? '' }}
                                </td>
                                <td>
                                    {{ $itemProductUnit->uom ?? '' }}
                                </td>
                                <td>
                                    @can('item_product_unit_show')
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.item-product-units.show', $itemProductUnit->id) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan

                                    @can('item_product_unit_edit')
                                        <a class="btn btn-xs btn-info" href="{{ route('admin.item-product-units.edit', $itemProductUnit->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan

                                    @can('item_product_unit_delete')
                                        <form action="{{ route('admin.item-product-units.destroy', $itemProductUnit->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                        </form>
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
@can('item_product_unit_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.item-product-units.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
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

  $.extend(true, $.fn.dataTable.defaults, {
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 10,
  });
  let table = $('.datatable-productUnitItemProductUnits:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection