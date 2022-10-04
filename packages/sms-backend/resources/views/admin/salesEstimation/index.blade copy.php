@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('cruds.report.title') }}
    </div>

    <div class="card-body">
        <div class="col-md-12 col-12 row mb-3">
            <div class="col-md-4">
                <label for="channel">Channel</label>
                <select class="form-control select2" name="channel[]" id="channel" multiple>
                    <option value=""></option>
                    @foreach($channels as $channel)
                        <option value="{{ $channel->id }}" {{ request('channel') == $channel->id ? 'selected' : '' }}>{{ $channel->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="productBrand">Product Brand</label>
                <select class="form-control select2" name="productBrand" id="productBrand">
                    <option value=""></option>
                    @foreach($productBrands as $productBrand)
                        <option value="{{ $productBrand->id }}" {{ request('productBrand') == $productBrand->id ? 'selected' : '' }}>{{ $productBrand->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="brandCategory">Brand Category</label>
                <select class="form-control select2" name="brandCategory[]" id="brandCategory" multiple>
                    <option value=""></option>
                    @foreach($brandCategories as $brandCategory)
                        <option value="{{ $brandCategory->id }}" {{ request('brandCategory') == $brandCategory->id ? 'selected' : '' }}>{{ $brandCategory->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-12 col-12 row mb-5">
            <div class="col-md-6">
                <label for="start_date">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control" placeholder="From Date"/>
            </div>
            <div class="col-md-6">
                <label for="end_date">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control" placeholder="To Date"/>
            </div>
        </div>
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Report">
            <thead>
                <tr>
                    <th rowspan="2" width="10" class="align-middle">No</th>
                    <th rowspan="2" class="align-middle">{{ trans('cruds.salesEstimation.fields.sales') }}</th>
                    <th rowspan="2" class="align-middle">{{ trans('cruds.salesEstimation.fields.customer') }}</th>
                    <th rowspan="2" class="align-middle">{{ trans('cruds.salesEstimation.fields.address') }}</th>
                    <th rowspan="2" class="align-middle">{{ trans('cruds.salesEstimation.fields.phone') }}</th>
                    <th rowspan="2" class="align-middle">{{ trans('cruds.salesEstimation.fields.source') }}</th>
                    <th rowspan="2" class="align-middle">{{ trans('cruds.salesEstimation.fields.start_date') }}</th>
                    <th rowspan="2" class="align-middle">{{ trans('cruds.salesEstimation.fields.remarks') }}</th>
                    <th colspan="{{ count($productBrands) }}" class="align-middle">{{ trans('cruds.salesEstimation.fields.product_brand') }}</th>
                    <th rowspan="2" class="align-middle">{{ trans('cruds.salesEstimation.fields.status') }}</th>
                </tr>
                <tr>
                    <!-- start dynamic datatable column -->
                    <script>
                        var tableColumns = [
                            {data: 'no', name: 'no' },
                            {data: 'sales_name', name: 'sales_name', class: 'sales_name'},
                            {data: 'customer_name', name: 'customer_name'},
                            {data: 'customer_address', name: 'customer_address'},
                            {data: 'customer_phone', name: 'customer_phone'},
                            {data: 'customer_source', name: 'customer_source'},
                            {data: 'customer_start_date', name: 'customer_start_date'},
                            {data: 'customer_remarks', name: 'customer_remarks'},
                            // {data: 'customer_estimated_value', name: 'customer_estimated_value'},
                        ]
                    </script>
                    @foreach($productBrands as $productBrand)
                        <th class="align-middle border-1">{{ strtoupper($productBrand->name) }}</th>

                        <script>
                            var {{ 'productBrandId_'.$productBrand->id }} = {data: '{{ 'productBrandId_'.$productBrand->id }}', name: '{{ 'productBrandId_'.$productBrand->id }}'}
                            tableColumns.push({{ 'productBrandId_'.$productBrand->id }})
                        </script>
                    @endforeach
                    <script>
                        tableColumns.push({data: 'activity_status', name: 'activity_status'})
                    </script>
                </tr>
            </thead>
        </table>
    </div>
</div>

@endsection
@section('scripts')
@parent
<script>
    $(function () {
        let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

        $.extend(true, $.fn.dataTable.defaults, {
            orderCellsTop: true,
            order: [[ 1, 'desc' ]],
            pageLength: 10,
        });

        let dtOverrideGlobals = {
            buttons: dtButtons,
            processing: true,
            serverSide: true,
            retrieve: true,
            aaSorting: [],
            ajax: "{{ route('admin.salesEstimation.index') }}",
            ajax: {
                url: "{{ route('admin.salesEstimation.index') }}",
                data: function(d) {
                    d.start_date = $('input[name=start_date]').val();
                    d.end_date = $('input[name=end_date]').val();
                    d.channel = $('#channel').val();
                    d.productBrand = $('#productBrand').val();
                    d.brandCategory = $('#brandCategory').val();
                }
            },
            columns: tableColumns,
            columnDefs:[
                {targets: [0,2,3,4,5,6,7,8,9], searchable: false},
                {targets: [8,9], orderable: false},
            ],
            orderCellsTop: true,
            order: [[1, 'desc']],
            pageLength: 10,
        };
        let table = $('.datatable-Report').DataTable(dtOverrideGlobals);
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

        $('#channel').change(function() {
            table.ajax.reload();
        });

        $('#productBrand').change(function() {
            table.ajax.reload();
        });

        $('#brandCategory').change(function() {
            table.ajax.reload();
        });

        $('input[name=start_date]').change(function() {
            table.ajax.reload();
        });

        $('input[name=end_date]').change(function() {
            table.ajax.reload();
        });

        table.on('column-visibility.dt', function(e, settings, column, state) {
            visibleColumnsIndexes = []
            table.columns(":visible").every(function(colIdx) {
                visibleColumnsIndexes.push(colIdx);
            });
        });

        // $(document).ajaxStart(function() {
        //     console.log(1);
        // });

        // $(document).ajaxStop(function() {
        //     console.log($('.sales_name')[0].next().text());
        //     if($('.sales_name')[0].next().text() == $(this).text()){
        //         $('.sales_name')[0].attr("rowspan", "2");
        //     }
        // });

    })

</script>
@endsection
