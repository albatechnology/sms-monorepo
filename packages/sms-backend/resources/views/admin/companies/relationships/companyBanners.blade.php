<div class="m-3">
    @can('banner_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.banners.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.banner.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.banner.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-companyBanners">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.banner.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.banner.fields.bannerable_type') }}
                            </th>
                            <th>
                                {{ trans('cruds.banner.fields.bannerable') }}
                            </th>
                            <th>
                                {{ trans('cruds.banner.fields.is_active') }}
                            </th>
                            <th>
                                {{ trans('cruds.banner.fields.start_time') }}
                            </th>
                            <th>
                                {{ trans('cruds.banner.fields.end_time') }}
                            </th>
                            <th>
                                {{ trans('cruds.banner.fields.company') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($banners as $key => $banner)
                            <tr data-entry-id="{{ $banner->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $banner->id ?? '' }}
                                </td>
                                <td>
                                    {{ $banner->bannerable_type ?? '' }}
                                </td>
                                <td>
                                    {{ $banner->bannerable ?? '' }}
                                </td>
                                <td>
                                    <span style="display:none">{{ $banner->is_active ?? '' }}</span>
                                    <input type="checkbox" disabled="disabled" {{ $banner->is_active ? 'checked' : '' }}>
                                </td>
                                <td>
                                    {{ $banner->start_time ?? '' }}
                                </td>
                                <td>
                                    {{ $banner->end_time ?? '' }}
                                </td>
                                <td>
                                    {{ $banner->company->name ?? '' }}
                                </td>
                                <td>
                                    @can('banner_show')
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.banners.show', $banner->id) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan

                                    @can('banner_edit')
                                        <a class="btn btn-xs btn-info" href="{{ route('admin.banners.edit', $banner->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan

                                    @can('banner_delete')
                                        <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('banner_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.banners.massDestroy') }}",
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
  let table = $('.datatable-companyBanners:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection