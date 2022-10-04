@extends('layouts.admin')
@section('content')
    @can('company_account_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.company-accounts.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.companyAccount.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.companyAccount.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-Company">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.companyAccount.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.company.title') }}
                        </th>
                        <th>
                            {{ trans('cruds.companyAccount.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.companyAccount.fields.bank_name') }}
                        </th>
                        <th>
                            {{ trans('cruds.companyAccount.fields.account_name') }}
                        </th>
                        <th>
                            {{ trans('cruds.companyAccount.fields.account_number') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($accounts as $key => $account)
                        <tr data-entry-id="{{ $account->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $account->id ?? '' }}
                            </td>
                            <td>
                                {{ $account->company->name ?? '' }}
                            </td>
                            <td>
                                {{ $account->name ?? '' }}
                            </td>
                            <td>
                                {{ $account->bank_name ?? '' }}
                            </td>
                            <td>
                                {{ $account->account_name ?? '' }}
                            </td>
                            <td>
                                {{ $account->account_number ?? '' }}
                            </td>
                            <td>
                                @can('company_account_show')
                                    <a class="btn btn-xs btn-primary"
                                       href="{{ route('admin.company-accounts.show', $account->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('company_account_edit')
                                    <a class="btn btn-xs btn-info"
                                       href="{{ route('admin.company-accounts.edit', $account->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('company_account_delete')
                                    <form action="{{ route('admin.company-accounts.destroy', $account->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                                          style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger"
                                               value="{{ trans('global.delete') }}">
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



@endsection
@section('scripts')
    @parent
    <script>
        $(function () {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

            $.extend(true, $.fn.dataTable.defaults, {
                orderCellsTop: true,
                order: [[1, 'desc']],
                pageLength: 100,
            });
            let table = $('.datatable-Company:not(.ajaxTable)').DataTable({buttons: dtButtons})
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function (e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })

    </script>
@endsection