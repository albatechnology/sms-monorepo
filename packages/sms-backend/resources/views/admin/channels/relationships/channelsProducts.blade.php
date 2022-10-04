<div class="m-3">
    @can('product_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.products.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.product.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.product.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-channelsProducts">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.product.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.product.fields.name') }}
                            </th>
                            <th>
                                {{ trans('cruds.product.fields.category') }}
                            </th>
                            <th>
                                {{ trans('cruds.product.fields.tag') }}
                            </th>
                            <th>
                                {{ trans('cruds.product.fields.price') }}
                            </th>
                            <th>
                                {{ trans('cruds.product.fields.photo') }}
                            </th>
                            <th>
                                {{ trans('cruds.product.fields.is_active') }}
                            </th>
                            <th>
                                {{ trans('cruds.product.fields.company') }}
                            </th>
                            <th>
                                {{ trans('cruds.product.fields.channels') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($products) && count($products) > 0)
                        @foreach($products as $key => $product)
                            <tr data-entry-id="{{ $product->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $product->id ?? '' }}
                                </td>
                                <td>
                                    {{ $product->name ?? '' }}
                                </td>
                                <td>
                                    @foreach($product->categories as $key => $item)
                                        <span class="badge badge-info">{{ $item->name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($product->tags as $key => $item)
                                        <span class="badge badge-info">{{ $item->name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    {{ $product->price ?? '' }}
                                </td>
                                <td>
                                    @foreach($product->photo as $key => $media)
                                        <a href="{{ $media->getUrl() }}" target="_blank" style="display: inline-block">
                                            <img src="{{ $media->getUrl('thumb') }}">
                                        </a>
                                    @endforeach
                                </td>
                                <td>
                                    <span style="display:none">{{ $product->is_active ?? '' }}</span>
                                    <input type="checkbox" disabled="disabled" {{ $product->is_active ? 'checked' : '' }}>
                                </td>
                                <td>
                                    {{ $product->company->name ?? '' }}
                                </td>
                                <td>
                                    @foreach($product->channels as $key => $item)
                                        <span class="badge badge-info">{{ $item->name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @can('product_show')
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.products.show', $product->id) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan

                                    @can('product_edit')
                                        <a class="btn btn-xs btn-info" href="{{ route('admin.products.edit', $product->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan

                                    @can('product_delete')
                                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                        </form>
                                    @endcan

                                </td>

                            </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="11"><div class="alert alert-warning">Data not found</div></td>
                        </tr>
                        @endif
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
@can('product_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.products.massDestroy') }}",
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
  let table = $('.datatable-channelsProducts:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });

})

</script>
@endsection
