@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.importBatch.title') }}
        </div>

        <div class="card-body">
            @livewire('import-batch-page', ['batch' => $batch])
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            {{ trans('cruds.importLine.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-ImportBatchLine">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th width="50">
                            {{ trans('cruds.importLine.fields.row') }}
                        </th>
                        <th>
                            {{ trans('cruds.importLine.fields.status') }}
                        </th>
                        <th>
                            {{ trans('cruds.importLine.fields.preview_status') }}
                        </th>
                        <th>
                            {{ trans('cruds.importLine.fields.errors') }}
                        </th>
                        <th>
                            {{ trans('cruds.productBrand.fields.name') }}
                        </th>
                        <th>

                        </th>
                    </tr>
                    <tr>
                        <td>
                        </td>
                        <td>
                            <input class="search w-100" type="text" strict="true"
                                   placeholder="{{ trans('global.search') }}">
                        </td>
                        <td>
                            <select class="search" strict="true">
                                <option value>{{ trans('global.all') }}</option>
                                @foreach(\App\Enums\Import\ImportLineStatus::getInstances() as $enum)
                                    <option value="{{ $enum->value }}">{{ $enum->description }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="search" strict="true">
                                <option value>{{ trans('global.all') }}</option>
                                @foreach(\App\Enums\Import\ImportLinePreviewStatus::getInstances() as $enum)
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

        let table;
        document.addEventListener('livewire:load', () => {
            window.livewire.on('reloadImportLineTable', () => table.ajax.reload())
        })

        $(function () {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

            let dtOverrideGlobals = {
                buttons: dtButtons,
                processing: true,
                serverSide: true,
                retrieve: true,
                aaSorting: [],
                ajax: "{{ route('admin.import-batches.import-lines.index', $batch->id) }}",
                columns: [
                    {data: 'placeholder', name: 'placeholder'},
                    {data: 'row', name: 'row'},
                    {data: 'status', name: 'status'},
                    {data: 'preview_status', name: 'preview_status'},
                    {data: 'errors', name: 'errors'},
                    {data: 'name', name: 'data.name'},
                    {data: 'actions', name: '{{ trans('global.actions') }}'}
                ],
                orderCellsTop: true,
                order: [[1, 'asc']],
                pageLength: 25,
            };
            table = $('.datatable-ImportBatchLine').DataTable(dtOverrideGlobals);
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
