@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.lead.title') }}
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.leads.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.lead.fields.id') }}
                            </th>
                            <td>
                                {{ $lead->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.lead.fields.type') }}
                            </th>
                            <td>
                                {{ $lead->type->description ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.lead.fields.status') }}
                            </th>
                            <td>
                                {{ $lead->status->label ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.lead.fields.customer') }}
                            </th>
                            <td>
                                {{ $lead->customer->getFullNameAttribute() ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.lead.fields.is_new_customer') }}
                            </th>
                            <td>
                                {!! $lead->is_new_customer ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-danger"></i>' !!}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.lead.fields.label') }}
                            </th>
                            <td>
                                {{ $lead->label }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.lead.fields.interest') }}
                            </th>
                            <td>
                                {{ $lead->interest }}
                            </td>
                        </tr>
                        <tr>
                            <th>Company</th>
                            <td>
                                {{ $lead->channel?->company?->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.lead.fields.channel') }}
                            </th>
                            <td>
                                {{ $lead->channel?->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Product Brand</th>
                            <td>
                                {{ $lead->productBrand?->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Handled Status</th>
                            <td>
                                {{ $lead->is_unhandled ? 'Unhandled' : 'Handled' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Handled By</th>
                            <td>
                                {{ $lead->user?->name .' - '. $lead->user->type->description }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.lead.fields.created_at') }}
                            </th>
                            <td>
                                {{ $lead->created_at ?? '' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.leads.index') }}">
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
                <a class="nav-link" href="#lead_activities" role="tab" data-toggle="tab" id="lead_activities_tab">
                    {{ trans('cruds.activity.title') }}
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane" role="tabpanel" id="lead_activities">
                @include('admin.leads.relationships.leadActivities', ['activities' => $lead->leadActivities])
            </div>
        </div>
    </div>
@endsection
