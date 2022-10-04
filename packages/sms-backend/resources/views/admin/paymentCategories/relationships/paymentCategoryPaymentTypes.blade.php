<div class="m-3">
    @can('payment_type_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.payment-types.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.paymentType.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.paymentType.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-paymentCategoryPaymentTypes">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.paymentType.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.paymentType.fields.name') }}
                            </th>
                            <th>
                                {{ trans('cruds.paymentType.fields.code') }}
                            </th>
                            <th>
                                {{ trans('cruds.paymentType.fields.payment_category') }}
                            </th>
                            <th>
                                {{ trans('cruds.paymentType.fields.require_approval') }}
                            </th>
                            <th>
                                {{ trans('cruds.paymentType.fields.company') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paymentTypes as $key => $paymentType)
                            <tr data-entry-id="{{ $paymentType->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $paymentType->id ?? '' }}
                                </td>
                                <td>
                                    {{ $paymentType->name ?? '' }}
                                </td>
                                <td>
                                    {{ $paymentType->code ?? '' }}
                                </td>
                                <td>
                                    {{ $paymentType->payment_category->name ?? '' }}
                                </td>
                                <td>
                                    <span style="display:none">{{ $paymentType->require_approval ?? '' }}</span>
                                    <input type="checkbox" disabled="disabled" {{ $paymentType->require_approval ? 'checked' : '' }}>
                                </td>
                                <td>
                                    {{ $paymentType->company->name ?? '' }}
                                </td>
                                <td>
                                    @can('payment_type_show')
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.payment-types.show', $paymentType->id) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan

                                    @can('payment_type_edit')
                                        <a class="btn btn-xs btn-info" href="{{ route('admin.payment-types.edit', $paymentType->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan

                                    @can('payment_type_delete')
                                        <form action="{{ route('admin.payment-types.destroy', $paymentType->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('payment_type_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.payment-types.massDestroy') }}",
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
  let table = $('.datatable-paymentCategoryPaymentTypes:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection