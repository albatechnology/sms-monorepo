<div class="m-3">
    @can('target_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.targets.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.target.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.target.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-targets">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.target.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.target.fields.type') }}
                        </th>
                        <th>
                            {{ trans('cruds.target.fields.target') }}
                        </th>
                        <th>
                            {{ trans('cruds.target.fields.value') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($targets as $model)
                        <tr data-entry-id="{{ $model->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $model->id ?? '' }}
                            </td>
                            <td>
                                {{ $model->type?->key }}
                            </td>
                            <td>
                                {{ $model->targetFormatted }}
                            </td>
                            <td>
                                {{ $model->valueFormatted }}
                            </td>
                            <td>
                                @can('target_show')
                                    <a class="btn btn-xs btn-primary"
                                       href="{{ route('admin.targets.show', $model->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('targets_edit')
                                    <a class="btn btn-xs btn-info"
                                       href="{{ route('admin.targets.edit', $model->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('target_delete')
                                    <form action="{{ route('admin.targets.destroy', $model->id) }}" method="POST"
                                          onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                                          style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger"
                                               value="{{ trans('global.delete') }}">
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
            @can('target_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.targets.massDestroy') }}",
                className: 'btn-danger',
                action: function (e, dt, node, config) {
                    var ids = $.map(dt.rows({selected: true}).nodes(), function (entry) {
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
                            data: {ids: ids, _method: 'DELETE'}
                        })
                            .done(function () {
                                location.reload()
                            })
                    }
                }
            }
            dtButtons.push(deleteButton)
            @endcan

            $.extend(true, $.fn.dataTable.defaults, {
                orderCellsTop: true,
                order: [[1, 'desc']],
                pageLength: 10,
            });
            let table = $('.datatable-targets:not(.ajaxTable)').DataTable({buttons: dtButtons})
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function (e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })

    </script>
@endsection