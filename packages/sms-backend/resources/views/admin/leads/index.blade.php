@extends('layouts.admin')
@section('content')
    @can('lead_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.leads.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.lead.title_singular') }}
                </a>
                <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                    {{ trans('global.app_csvImport') }}
                </button>
                {{-- @include('csvImport.customModal', ['model' => 'Lead', 'route' => 'admin.leads.parseCsvImport', 'type' => \App\Enums\Import\ImportBatchType::LEAD]) --}}
                <div class="modal fade" id="csvImportModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabel">@lang('global.app_csvImport')</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class='row'>
                                    <div class='col-md-12'>
                                        <a
                                            href="{{ route('admin.exports.sample', \App\Enums\Import\ImportBatchType::LEAD) }}">Download
                                            Sample CSV</a>
                                        <br>
                                        <br>
                                        <form class="form-horizontal form-import" method="POST" action="{{ route('admin.leads.import') }}" enctype="multipart/form-data">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="type"
                                                value="{{ \App\Enums\Import\ImportBatchType::LEAD }}">
                                            <div class="form-group{{ $errors->has('csv_file') ? ' has-error' : '' }}">
                                                <label for="csv_file"
                                                    class="control-label">@lang('global.app_csv_file_to_import')</label>
                                                <input id="csv_file" type="file" class="form-control-file" name="csv_file"
                                                    required>

                                                @if ($errors->has('csv_file'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('csv_file') }}</strong>
                                                    </span>
                                                @endif
                                                <br>
                                            </div>
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary" id="btn_import">
                                                    @lang('global.app_parse_csv')
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan
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
                            <label>Product Brand</label>
                            <select name="product_brand_id" id="product_brand_id" class="form-control">
                                <option value="">- All -</option>
                                @foreach ($productBrands as $id => $name)
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
                       <div class="row">
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
            {{ trans('cruds.lead.title_singular') }} {{ trans('global.list') }}
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
                        <th>Subscriber</th>
                        <th>
                            {{ trans('cruds.lead.fields.sales') }}
                        </th>
                        <th>
                            {{ trans('cruds.lead.fields.type') }}
                        </th>
                        <th>
                            {{ trans('cruds.lead.fields.status') }}
                        </th>
                        <th>Handled?</th>
                        <th>
                            {{ trans('cruds.lead.fields.is_new_customer') }}
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
                            <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                        </td>
                        <td>
                            <select class="search">
                                <option value>{{ trans('global.all') }}</option>
                                @foreach ($sales as $key => $item)
                                    <option value="{{ $item->name }}">{{ $item->name }}</option>
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
                        <td></td>
                        <td></td>
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
                                @foreach ($channels as $key => $item)
                                    <option value="{{ $item->name }}">{{ $item->name }}</option>
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
                url: "{{ route('admin.leads.massDestroy') }}",
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
                    url: "{{ route('admin.leads.index') }}",
                    data: function(d) {
                        // d.company_id = $('#company_id').val();
                        d.supervisor_id = $('#supervisor_id').val();
                        d.lead_category_id = $('#lead_category_id').val();
                        d.sub_lead_category_id = $('#sub_lead_category_id').val();
                        d.product_brand_id = $('#product_brand_id').val();
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
                        data: 'subscriber',
                        name: 'channel.subscribtionUser.name'
                    },
                    {
                        data: 'sales',
                        name: 'sales'
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
                        data: 'is_unhandled',
                        name: 'is_unhandled'
                    },
                    {
                        data: 'is_new_customer',
                        name: 'is_new_customer'
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
                        data: 'channel_name',
                        name: 'channel.name'
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
            })
            $('.form-import').on('submit', function(){
                $('#btn_import').attr('disabled', true).text('Importing...');
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
            $('#product_brand_id').change(function() {
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
