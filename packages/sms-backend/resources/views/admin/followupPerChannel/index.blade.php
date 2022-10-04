@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.followupPerChannels.title') }}
        </div>
        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Report">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th>Channel</th>
                        <th>{{ trans('global.month') }}</th>
                        <th>{{ trans('cruds.followupPerChannels.fields.total_activities') }}</th>
                        <th>{{ trans('cruds.followupPerChannels.fields.total_leads') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        $(function() {
            let dtOverrideGlobals = {
                processing: true,
                serverSide: true,
                retrieve: true,
                aaSorting: [],
                ajax: "{{ route('admin.followup-per-channels.getData') }}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'total_activities',
                        name: 'total_activities'
                    },
                    {
                        data: 'total_leads',
                        name: 'total_leads'
                    },
                ],
                // orderCellsTop: true,
                order: [
                    [2, 'asc']
                ],
                // pageLength: 10,
            };
            let table = $('.datatable-Report').DataTable(dtOverrideGlobals);
        });
    </script>
@endsection
