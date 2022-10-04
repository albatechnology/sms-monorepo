@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">{{ trans('global.create') }} {{ trans('cruds.importBatch.title_singular') }}</div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.exports.model') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="required" for="companies">{{ trans('cruds.user.fields.company') }}</label>
                    <select class="form-control select2 {{ $errors->has('company') ? 'is-invalid' : '' }}"
                        name="company_id" id="company">
                        @foreach ($companies as $id => $name)
                            <option value="{{ $id }}" {{ $id == old('company_id', 0) ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('company'))
                        <span class="text-danger">{{ $errors->first('company') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.user.fields.company_helper') }}</span>
                </div>

                <div class="form-group">
                    <label class="required" for="type">{{ trans('cruds.export.fields.type') }}</label>
                    <select class="form-control select2 {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type"
                        id="type">
                        @foreach (\App\Enums\Import\ImportBatchType::getInstances() as $enum)
                            <option value="{{ $enum->value }}"
                                {{ $enum->value == old('type', '') ? 'selected' : '' }}>{{ $enum->description }}
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->has('type'))
                        <span class="text-danger">{{ $errors->first('type') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.export.fields.type_helper') }}</span>
                </div>

                <div class="form-group">
                    <label for="start_id">{{ trans('cruds.export.fields.start_id') }}</label>
                    <input class="form-control {{ $errors->has('start_id') ? 'is-invalid' : '' }}" type="number"
                        name="start_id" id="start_id" value="{{ old('start_id', '') }}">
                    @if ($errors->has('start_id'))
                        <span class="text-danger">{{ $errors->first('start_id') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.export.fields.start_id_helper') }}</span>
                </div>

                <div class="form-group">
                    <label for="end_id">{{ trans('cruds.export.fields.end_id') }}</label>
                    <input class="form-control {{ $errors->has('end_id') ? 'is-invalid' : '' }}" type="number"
                        name="end_id" id="end_id" value="{{ old('end_id', '') }}">
                    @if ($errors->has('end_id'))
                        <span class="text-danger">{{ $errors->first('end_id') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.export.fields.end_id_helper') }}</span>
                </div>


                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header">List Exports</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-Role">
                    <thead>
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>Exported By</th>
                            <th>Title</th>
                            <th>File</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($exports as $export)
                            <tr>
                                <td></td>
                                <td>
                                    {{ $export->id ?? '' }}
                                </td>
                                <td>
                                    {{ $export->user?->name ?? '' }}
                                </td>
                                <td>
                                    {{ $export->title ?? '' }}
                                </td>
                                <td>
                                    @foreach ($export->file as $key => $media)
                                        <a href="{{ $media->getUrl() }}" target="_blank" class="btn btn-danger"><i class="fa fa-download"></i> Download</a>
                                    @endforeach
                                </td>
                                <td>
                                    @if ($export->status == 0)
                                        In Progress
                                    @elseif ($export->status == 1)
                                        Success
                                    @else
                                        Failed
                                    @endif
                                </td>
                                <td>
                                    {{ date('d-m-Y H:i', strtotime($export->created_at)) }}
                                </td>
                                <td>
                                    <form action="{{ route('admin.exports.destroy', $export->id) }}" method="POST"
                                        onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                                        style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger"
                                            value="{{ trans('global.delete') }}">
                                    </form>
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
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

            $.extend(true, $.fn.dataTable.defaults, {
                orderCellsTop: true,
                order: [
                    [1, 'desc']
                ],
                pageLength: 100,
            });
            let table = $('.datatable-export:not(.ajaxTable)').DataTable({
                buttons: dtButtons
            })
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })
    </script>
@endsection
