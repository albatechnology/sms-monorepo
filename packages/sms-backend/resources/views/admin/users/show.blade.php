@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.user.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.users.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.id') }}
                        </th>
                        <td>
                            {{ $user->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.name') }}
                        </th>
                        <td>
                            {{ $user->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.email') }}
                        </th>
                        <td>
                            {{ $user->email }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.email_verified_at') }}
                        </th>
                        <td>
                            {{ $user->email_verified_at }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.roles') }}
                        </th>
                        <td>
                            @foreach($user->roles as $key => $roles)
                                <span class="label label-info">{{ $roles->title }}</span>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.type') }}
                        </th>
                        <td>
                            {{ $user->type->description ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.supervisor_type') }}
                        </th>
                        <td>
                            {{ $user->supervisor_type->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.supervisor') }}
                        </th>
                        <td>
                            {{ $user->supervisor->name ?? '' }}
                        </td>
                    </tr>
                    {{-- <tr>
                        <th>
                            {{ trans('cruds.user.fields.companies') }}
                        </th>
                        <td>
                            @foreach($user->companies as $key => $companies)
                                <span class="label label-info">{{ $companies->name }}</span>
                            @endforeach
                        </td>
                    </tr> --}}
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.channels') }}
                        </th>
                        <td>
                            @foreach($user->channels as $key => $channels)
                                <span class="label label-info">{{ $channels->name }}</span>
                            @endforeach
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.users.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        {{ trans('global.relatedData') }}
    </div>
    <ul class="nav nav-tabs" role="tablist" id="relationship-tabs">
        <li class="nav-item">
            <a class="nav-link" href="#user_activities" role="tab" data-toggle="tab">
                {{ trans('cruds.activity.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#user_activity_comments" role="tab" data-toggle="tab">
                {{ trans('cruds.activityComment.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#user_orders" role="tab" data-toggle="tab">
                {{ trans('cruds.order.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#approved_by_payments" role="tab" data-toggle="tab">
                {{ trans('cruds.payment.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#fulfilled_by_shipments" role="tab" data-toggle="tab">
                {{ trans('cruds.shipment.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#fulfilled_by_invoices" role="tab" data-toggle="tab">
                {{ trans('cruds.invoice.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#supervisor_users" role="tab" data-toggle="tab">
                {{ trans('cruds.user.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#requested_by_stock_transfers" role="tab" data-toggle="tab">
                {{ trans('cruds.stockTransfer.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#approved_by_stock_transfers" role="tab" data-toggle="tab">
                {{ trans('cruds.stockTransfer.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#user_user_alerts" role="tab" data-toggle="tab">
                {{ trans('cruds.userAlert.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="user_activities">
            @includeIf('admin.users.relationships.userActivities', ['activities' => $user->userActivities])
        </div>
        <div class="tab-pane" role="tabpanel" id="user_activity_comments">
            @includeIf('admin.users.relationships.userActivityComments', ['activityComments' => $user->userActivityComments])
        </div>
        <div class="tab-pane" role="tabpanel" id="user_orders">
            @includeIf('admin.users.relationships.userOrders', ['orders' => $user->userOrders])
        </div>
        <div class="tab-pane" role="tabpanel" id="approved_by_payments">
            @includeIf('admin.users.relationships.approvedByPayments', ['payments' => $user->approvedByPayments])
        </div>
        <div class="tab-pane" role="tabpanel" id="fulfilled_by_shipments">
            @includeIf('admin.users.relationships.fulfilledByShipments', ['shipments' => $user->fulfilledByShipments])
        </div>
        <div class="tab-pane" role="tabpanel" id="fulfilled_by_invoices">
            @includeIf('admin.users.relationships.fulfilledByInvoices', ['invoices' => $user->fulfilledByInvoices])
        </div>
        <div class="tab-pane" role="tabpanel" id="supervisor_users">
            @includeIf('admin.users.relationships.supervisorUsers', ['users' => $user->supervisorUsers])
        </div>
        <div class="tab-pane" role="tabpanel" id="requested_by_stock_transfers">
            @includeIf('admin.users.relationships.requestedByStockTransfers', ['stockTransfers' => $user->requestedByStockTransfers])
        </div>
        <div class="tab-pane" role="tabpanel" id="approved_by_stock_transfers">
            @includeIf('admin.users.relationships.approvedByStockTransfers', ['stockTransfers' => $user->approvedByStockTransfers])
        </div>
        <div class="tab-pane" role="tabpanel" id="user_user_alerts">
            @includeIf('admin.users.relationships.userUserAlerts', ['userAlerts' => $user->userUserAlerts])
        </div>
    </div>
</div>

@endsection
