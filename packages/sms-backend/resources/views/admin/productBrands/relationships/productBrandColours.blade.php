<div class="m-3">
    @can('colour_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.colours.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.colour.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.colour.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-productBrandColours">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.colour.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.colour.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.colour.fields.photo') }}
                        </th>
                        <th>
                            {{ trans('cruds.colour.fields.product_brand') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($colours as $key => $colour)
                        <tr data-entry-id="{{ $colour->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $colour->id ?? '' }}
                            </td>
                            <td>
                                {{ $colour->name ?? '' }}
                            </td>
                            <td>
                                @foreach($colour->photo as $key => $media)
                                    <a href="{{ $media->getUrl() }}" target="_blank" style="display: inline-block">
                                        <img src="{{ $media->getUrl('thumb') }}">
                                    </a>
                                @endforeach
                            </td>
                            <td>
                                {{ $colour->productBrand->name ?? '' }}
                            </td>
                            <td>
                                @can('colour_show')
                                    <a class="btn btn-xs btn-primary"
                                       href="{{ route('admin.colours.show', $colour->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('colour_edit')
                                    <a class="btn btn-xs btn-info"
                                       href="{{ route('admin.colours.edit', $colour->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('colour_delete')
                                    <form action="{{ route('admin.colours.destroy', $colour->id) }}" method="POST"
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
            @can('colour_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.colours.massDestroy') }}",
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
                pageLength: 25,
            });
            let table = $('.datatable-productBrandColours:not(.ajaxTable)').DataTable({buttons: dtButtons})
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function (e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
            $('div#sidebar').on('transitionend', function (e) {
                $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
            })

        })

    </script>
@endsection