<div class="m-3">
    @can('activity_comment_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.activity-comments.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.activityComment.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.activityComment.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-userActivityComments">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.activityComment.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.activityComment.fields.content') }}
                            </th>
                            <th>
                                {{ trans('cruds.activityComment.fields.user') }}
                            </th>
                            <th>
                                {{ trans('cruds.activityComment.fields.activity') }}
                            </th>
                            <th>
                                {{ trans('cruds.activityComment.fields.activity_comment') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activityComments as $key => $activityComment)
                            <tr data-entry-id="{{ $activityComment->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $activityComment->id ?? '' }}
                                </td>
                                <td>
                                    {{ $activityComment->content ?? '' }}
                                </td>
                                <td>
                                    {{ $activityComment->user->name ?? '' }}
                                </td>
                                <td>
                                    {{ $activityComment->activity->follow_up_datetime ?? '' }}
                                </td>
                                <td>
                                    {{ $activityComment->activity_comment->content ?? '' }}
                                </td>
                                <td>
                                    @can('activity_comment_show')
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.activity-comments.show', $activityComment->id) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan

                                    @can('activity_comment_edit')
                                        <a class="btn btn-xs btn-info" href="{{ route('admin.activity-comments.edit', $activityComment->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan

                                    @can('activity_comment_delete')
                                        <form action="{{ route('admin.activity-comments.destroy', $activityComment->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('activity_comment_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.activity-comments.massDestroy') }}",
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
    pageLength: 25,
  });
  let table = $('.datatable-userActivityComments:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection