@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-body">
        <form id="form-filter">
            @csrf
            <div class="row">
                {{-- <div class="col-md-6">
                    <div class="form-group">
                        <label>Company</label>
                        <select name="company_id" id="company_id" class="form-control">
                            <option value="">- All -</option>
                            @foreach ($companies as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div> --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label>BUM</label>
                        <select name="supervisor_id" id="supervisor_id" class="form-control">
                            <option value="">- All -</option>
                            @foreach ($supervisors as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Lead Category</label>
                        <select name="lead_category_id" id="lead_category_id" class="form-control">
                            <option value="">- All -</option>
                            @foreach ($leadCategories as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Lead Sub Category</label>
                        <select name="sub_lead_category_id" id="sub_lead_category_id" class="form-control" disabled>
                            <option value="">- All -</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control">
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.unhandleLead.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Lead">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.lead.fields.id') }}
                        </th>
                        <th>Supervisor</th>
                        <th>
                            {{ trans('cruds.lead.fields.type') }}
                        </th>
                        <th>
                            {{ trans('cruds.lead.fields.status') }}
                        </th>
                        <th>
                            {{ trans('cruds.lead.fields.label') }}
                        </th>
                        <th>
                            {{ trans('cruds.lead.fields.customer') }}
                        </th>
                        <th>
                            {{ trans('cruds.lead.fields.channel') }}
                        </th>
                        <th>
                            {{ trans('cruds.lead.fields.created_at') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    <tr>
                        <td>
                        </td>
                        <td>
                            <input class="search" type="text" strict="true"
                                placeholder="{{ trans('global.search') }}">
                        </td>
                        <td>
                            <select class="search">
                                <option value>{{ trans('global.all') }}</option>
                                @foreach ($supervisors as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="search" strict="true">
                                <option value>{{ trans('global.all') }}</option>
                                @foreach (App\Enums\LeadType::getInstances() as $enum)
                                    <option value="{{ $enum->value }}">{{ $enum->label }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="search" strict="true">
                                <option value>{{ trans('global.all') }}</option>
                                @foreach (App\Enums\LeadStatus::getInstances() as $enum)
                                    <option value="{{ $enum->value }}">{{ $enum->label }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                        </td>
                        <td>
                            <select class="search">
                                <option value>{{ trans('global.all') }}</option>
                                @foreach ($customers as $key => $item)
                                    <option value="{{ $item->first_name }}">{{ $item->first_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="search">
                                <option value>{{ trans('global.all') }}</option>
                                @foreach ($channels as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                        </td>
                        <td></td>
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
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            @can('lead_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
                let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.unhandle-leads.massDestroy') }}",
                className: 'btn-danger',
                action: function (e, dt, node, config) {
                var ids = $.map(dt.rows({ selected: true }).data(), function (entry) {
                return entry.id
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

            let dtOverrideGlobals = {
                buttons: dtButtons,
                processing: true,
                serverSide: true,
                retrieve: true,
                aaSorting: [],
                ajax: {
                    url: "{{ route('admin.unhandle-leads.index') }}",
                    data: function(d) {
                        // d.company_id = $('#company_id').val();
                        d.supervisor_id = $('#supervisor_id').val();
                        d.lead_category_id = $('#lead_category_id').val();
                        d.sub_lead_category_id = $('#sub_lead_category_id').val();
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'user_id',
                        name: 'user_id'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'label',
                        name: 'label'
                    },
                    {
                        data: 'customer_first_name',
                        name: 'customer.first_name'
                    },
                    {
                        data: 'channel_id',
                        name: 'channel_id',
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'actions',
                        name: '{{ trans('global.actions') }}'
                    }
                ],
                orderCellsTop: true,
                order: [
                    [1, 'desc']
                ],
                pageLength: 10,
            };
            let table = $('.datatable-Lead').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

            let visibleColumnsIndexes = null;
            $('.datatable thead').on('input', '.search', function() {
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
            });

            // $('#company_id').change(function() {
            //     table.ajax.reload();
            // });
            $('#supervisor_id').change(function() {
                table.ajax.reload();
            });
            $('#lead_category_id').change(function() {
                if($(this).val()){
                    $.get("{{ url('admin/leads/get-sublead-categories') }}/"+$(this).val(), function(html){
                        $('#sub_lead_category_id').attr('disabled', false).html(html);
                    })
                } else {
                    $('#sub_lead_category_id').attr('disabled', true).html('<option value="">- All -</option>');
                }
                table.ajax.reload();
            });
            $('#sub_lead_category_id').change(function() {
                table.ajax.reload();
            });
            $('#start_date').change(function() {
                table.ajax.reload();
            });
            $('#end_date').change(function() {
                table.ajax.reload();
            });
        });
    </script>
@endsection
