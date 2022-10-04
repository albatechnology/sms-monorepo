<div class="m-3">
    @can('tax_invoice_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.tax-invoices.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.taxInvoice.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.taxInvoice.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-addressTaxInvoices">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.taxInvoice.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.taxInvoice.fields.customer') }}
                            </th>
                            <th>
                                {{ trans('cruds.customer.fields.last_name') }}
                            </th>
                            <th>
                                {{ trans('cruds.taxInvoice.fields.company_name') }}
                            </th>
                            <th>
                                {{ trans('cruds.taxInvoice.fields.npwp') }}
                            </th>
                            <th>
                                {{ trans('cruds.taxInvoice.fields.address') }}
                            </th>
                            <th>
                                {{ trans('cruds.taxInvoice.fields.email') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($taxInvoices as $key => $taxInvoice)
                            <tr data-entry-id="{{ $taxInvoice->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $taxInvoice->id ?? '' }}
                                </td>
                                <td>
                                    {{ $taxInvoice->customer->first_name ?? '' }}
                                </td>
                                <td>
                                    {{ $taxInvoice->customer->last_name ?? '' }}
                                </td>
                                <td>
                                    {{ $taxInvoice->company_name ?? '' }}
                                </td>
                                <td>
                                    {{ $taxInvoice->npwp ?? '' }}
                                </td>
                                <td>
                                    {{ $taxInvoice->address->address_line_1 ?? '' }}
                                </td>
                                <td>
                                    {{ $taxInvoice->email ?? '' }}
                                </td>
                                <td>
                                    @can('tax_invoice_show')
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.tax-invoices.show', $taxInvoice->id) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan

                                    @can('tax_invoice_edit')
                                        <a class="btn btn-xs btn-info" href="{{ route('admin.tax-invoices.edit', $taxInvoice->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan

                                    @can('tax_invoice_delete')
                                        <form action="{{ route('admin.tax-invoices.destroy', $taxInvoice->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
</div>
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('tax_invoice_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.tax-invoices.massDestroy') }}",
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
    pageLength: 10,
  });
  let table = $('.datatable-addressTaxInvoices:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection