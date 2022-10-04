<div class="m-3">
    @can('promo_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.promos.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.promo.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.promo.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-productunitsPromos">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.promo.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.promo.fields.name') }}
                            </th>
                            <th>
                                {{ trans('cruds.promo.fields.image') }}
                            </th>
                            <th>
                                {{ trans('cruds.promo.fields.start_date') }}
                            </th>
                            <th>
                                {{ trans('cruds.promo.fields.end_date') }}
                            </th>
                            <th>
                                {{ trans('cruds.promo.fields.channel') }}
                            </th>
                            <th>
                                {{ trans('cruds.promo.fields.productunits') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($promos as $key => $promo)
                            <tr data-entry-id="{{ $promo->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $promo->id ?? '' }}
                                </td>
                                <td>
                                    {{ $promo->name ?? '' }}
                                </td>
                                <td>
                                    @foreach($promo->image as $key => $media)
                                        <a href="{{ $media->getUrl() }}" target="_blank" style="display: inline-block">
                                            <img src="{{ $media->getUrl('thumb') }}">
                                        </a>
                                    @endforeach
                                </td>
                                <td>
                                    {{ $promo->start_date ?? '' }}
                                </td>
                                <td>
                                    {{ $promo->end_date ?? '' }}
                                </td>
                                <td>
                                    {{ $promo->channel->name ?? '' }}
                                </td>
                                <td>
                                    @foreach($promo->productunits as $key => $item)
                                        <span class="badge badge-info">{{ $item->name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @can('promo_show')
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.promos.show', $promo->id) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan

                                    @can('promo_edit')
                                        <a class="btn btn-xs btn-info" href="{{ route('admin.promos.edit', $promo->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan

                                    @can('promo_delete')
                                        <form action="{{ route('admin.promos.destroy', $promo->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('promo_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.promos.massDestroy') }}",
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
    pageLength: 100,
  });
  let table = $('.datatable-productunitsPromos:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection