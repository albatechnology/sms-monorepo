<div class="m-3">
    @can('discount_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.discounts.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.discount.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.discount.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-companyDiscounts">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.discount.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.discount.fields.description') }}
                            </th>
                            <th>
                                {{ trans('cruds.discount.fields.type') }}
                            </th>
                            <th>
                                {{ trans('cruds.discount.fields.activation_code') }}
                            </th>
                            <th>
                                {{ trans('cruds.discount.fields.value') }}
                            </th>
                            <th>
                                {{ trans('cruds.discount.fields.scope') }}
                            </th>
                            <th>
                                {{ trans('cruds.discount.fields.start_time') }}
                            </th>
                            <th>
                                {{ trans('cruds.discount.fields.end_time') }}
                            </th>
                            <th>
                                {{ trans('cruds.discount.fields.is_active') }}
                            </th>
                            <th>
                                {{ trans('cruds.discount.fields.max_discount_price_per_order') }}
                            </th>
                            <th>
                                {{ trans('cruds.discount.fields.max_use_per_customer') }}
                            </th>
                            <th>
                                {{ trans('cruds.discount.fields.min_order_price') }}
                            </th>
                            <th>
                                {{ trans('cruds.discount.fields.company') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($discounts as $key => $discount)
                            <tr data-entry-id="{{ $discount->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $discount->id ?? '' }}
                                </td>
                                <td>
                                    {{ $discount->description ?? '' }}
                                </td>
                                <td>
                                    {{ App\Models\Discount::TYPE_SELECT[$discount->type] ?? '' }}
                                </td>
                                <td>
                                    {{ $discount->activation_code ?? '' }}
                                </td>
                                <td>
                                    {{ $discount->value ?? '' }}
                                </td>
                                <td>
                                    {{ App\Models\Discount::SCOPE_SELECT[$discount->scope] ?? '' }}
                                </td>
                                <td>
                                    {{ $discount->start_time ?? '' }}
                                </td>
                                <td>
                                    {{ $discount->end_time ?? '' }}
                                </td>
                                <td>
                                    <span style="display:none">{{ $discount->is_active ?? '' }}</span>
                                    <input type="checkbox" disabled="disabled" {{ $discount->is_active ? 'checked' : '' }}>
                                </td>
                                <td>
                                    {{ $discount->max_discount_price_per_order ?? '' }}
                                </td>
                                <td>
                                    {{ $discount->max_use_per_customer ?? '' }}
                                </td>
                                <td>
                                    {{ $discount->min_order_price ?? '' }}
                                </td>
                                <td>
                                    {{ $discount->company->name ?? '' }}
                                </td>
                                <td>
                                    @can('discount_show')
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.discounts.show', $discount->id) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan

                                    @can('discount_edit')
                                        <a class="btn btn-xs btn-info" href="{{ route('admin.discounts.edit', $discount->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan

                                    @can('discount_delete')
                                        <form action="{{ route('admin.discounts.destroy', $discount->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('discount_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.discounts.massDestroy') }}",
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
  let table = $('.datatable-companyDiscounts:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection