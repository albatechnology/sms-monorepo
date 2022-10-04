@extends('layouts.admin')
@section('content')
@can('target_schedule_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.target-schedules.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.targetSchedule.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.targetSchedule.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-TargetSchedule">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.targetSchedule.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.targetSchedule.fields.duration_type') }}
                        </th>
                        <th>
                            {{ trans('cruds.targetSchedule.fields.start_date') }}
                        </th>
                        <th>
                            {{ trans('cruds.targetSchedule.fields.target_name') }}
                        </th>
                        <th>
                            {{ trans('cruds.targetSchedule.fields.value') }}
                        </th>
                        <th>
                            {{ trans('cruds.targetSchedule.fields.target_value_type') }}
                        </th>
                        <th>
                            {{ trans('cruds.targetSchedule.fields.target_type') }}
                        </th>
                        <th>
                            {{ trans('cruds.targetSchedule.fields.target_subject') }}
                        </th>
                        <th>
                            {{ trans('cruds.targetSchedule.fields.target_subject_type') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    <tr>
                        <td>
                        </td>
                        <td>
                            <input class="search" type="text" strict="true" placeholder="{{ trans('global.search') }}">
                        </td>
                        <td>
                            <select class="search" strict="true">
                                <option value>{{ trans('global.all') }}</option>
                                @foreach(App\Models\TargetSchedule::DURATION_TYPE_SELECT as $key => $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                        </td>
                        <td>
                            <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                        </td>
                        <td>
                            <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                        </td>
                        <td>
                            <select class="search" strict="true">
                                <option value>{{ trans('global.all') }}</option>
                                @foreach(App\Models\TargetSchedule::TARGET_VALUE_TYPE_SELECT as $key => $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="search" strict="true">
                                <option value>{{ trans('global.all') }}</option>
                                @foreach(App\Models\TargetSchedule::TARGET_TYPE_SELECT as $key => $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="search" strict="true">
                                <option value>{{ trans('global.all') }}</option>
                                @foreach(App\Models\TargetSchedule::TARGET_SUBJECT_SELECT as $key => $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="search" strict="true">
                                <option value>{{ trans('global.all') }}</option>
                                @foreach(App\Models\TargetSchedule::TARGET_SUBJECT_TYPE_SELECT as $key => $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                        </td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($targetSchedules as $key => $targetSchedule)
                        <tr data-entry-id="{{ $targetSchedule->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $targetSchedule->id ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\TargetSchedule::DURATION_TYPE_SELECT[$targetSchedule->duration_type] ?? '' }}
                            </td>
                            <td>
                                {{ $targetSchedule->start_date ?? '' }}
                            </td>
                            <td>
                                {{ $targetSchedule->target_name ?? '' }}
                            </td>
                            <td>
                                {{ $targetSchedule->value ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\TargetSchedule::TARGET_VALUE_TYPE_SELECT[$targetSchedule->target_value_type] ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\TargetSchedule::TARGET_TYPE_SELECT[$targetSchedule->target_type] ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\TargetSchedule::TARGET_SUBJECT_SELECT[$targetSchedule->target_subject] ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\TargetSchedule::TARGET_SUBJECT_TYPE_SELECT[$targetSchedule->target_subject_type] ?? '' }}
                            </td>
                            <td>
                                @can('target_schedule_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.target-schedules.show', $targetSchedule->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('target_schedule_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.target-schedules.edit', $targetSchedule->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('target_schedule_delete')
                                    <form action="{{ route('admin.target-schedules.destroy', $targetSchedule->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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



@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('target_schedule_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.target-schedules.massDestroy') }}",
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
  let table = $('.datatable-TargetSchedule:not(.ajaxTable)').DataTable({ buttons: dtButtons })
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
})

</script>
@endsection