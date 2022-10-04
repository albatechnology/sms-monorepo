@extends('layouts.admin')
@section('content')
    {{--    @can('company_create')--}}
    {{--        <div style="margin-bottom: 10px;" class="row">--}}
    {{--            <div class="col-lg-12">--}}
    {{--                <a class="btn btn-success" href="{{ route('admin.import-batches.create') }}">--}}
    {{--                    {{ trans('global.add') }} {{ trans('cruds.importBatch.title_singular') }}--}}
    {{--                </a>--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--    @endcan--}}
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.importBatch.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-ImportBatch">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th width="50">
                            {{ trans('cruds.importBatch.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.importBatch.fields.type') }}
                        </th>
                        <th>
                            {{ trans('cruds.importBatch.fields.filename') }}
                        </th>
                        <th>
                            {{ trans('cruds.importBatch.fields.status') }}
                        </th>
                        <th>
                            {{ trans('cruds.importBatch.fields.summary') }}
                        </th>
                        <th>
                            {{ trans('cruds.importBatch.fields.preview_summary') }}
                        </th>
                        <th>

                        </th>
                    </tr>
                    <tr>
                        <td>
                        </td>
                        <td>
                            <input class="search w-100" type="text" strict="true" strict="true"
                                   placeholder="{{ trans('global.search') }}">
                        </td>
                        <td>
                            <select class="search" strict="true">
                                <option value>{{ trans('global.all') }}</option>
                                @foreach(\App\Enums\Import\ImportBatchType::getInstances() as $enum)
                                    <option value="{{ $enum->value }}">{{ $enum->description }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                        </td>
                        <td>
                            <select class="search" strict="true">
                                <option value>{{ trans('global.all') }}</option>
                                @foreach(\App\Enums\Import\ImportBatchStatus::getInstances() as $enum)
                                    <option value="{{ $enum->value }}">{{ $enum->description }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                        </td>
                        <td>
                            <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                        </td>
                        <td>
                        </td>
                    </tr>
                    </thead>
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

            let dtOverrideGlobals = {
                buttons: dtButtons,
                processing: true,
                serverSide: true,
                retrieve: true,
                aaSorting: [],
                ajax: "{{ route('admin.import-batches.index') }}",
                columns: [
                    {data: 'placeholder', name: 'placeholder'},
                    {data: 'id', name: 'id'},
                    {data: 'type', name: 'type'},
                    {data: 'filename', name: 'filename'},
                    {data: 'status', name: 'status'},
                    {data: 'summary', name: 'summary'},
                    {data: 'preview_summary', name: 'preview_summary'},
                    {data: 'actions', name: '{{ trans('global.actions') }}'}
                ],
                orderCellsTop: true,
                order: [[1, 'desc']],
                pageLength: 25,
            };
            let table = $('.datatable-ImportBatch').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function (e) {
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
            table.on('column-visibility.dt', function (e, settings, column, state) {
                visibleColumnsIndexes = []
                table.columns(":visible").every(function (colIdx) {
                    visibleColumnsIndexes.push(colIdx);
                });
            })

        })

    </script>
@endsection