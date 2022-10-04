@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.activity.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.activities.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.activity.fields.id') }}
                        </th>
                        <td>
                            {{ $activity->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.activity.fields.user') }}
                        </th>
                        <td>
                            {{ $activity->user->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.activity.fields.customer') }}
                        </th>
                        <td>
                            {{ $activity->customer->getFullNameAttribute() ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.activity.fields.lead') }}
                        </th>
                        <td>
                            {{ $activity->lead->label ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.activity.fields.follow_up_datetime') }}
                        </th>
                        <td>
                            {{ $activity->follow_up_datetime }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.activity.fields.follow_up_method') }}
                        </th>
                        <td>
                            {{ $activity->follow_up_method?->description }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.activity.fields.feedback') }}
                        </th>
                        <td>
                            {{ $activity->feedback }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.activity.fields.status') }}
                        </th>
                        <td>
                            {{ $activity->status?->description }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.activities.index') }}">
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
            <a class="nav-link" href="#activity_activity_comments" role="tab" data-toggle="tab">
                {{ trans('cruds.activityComment.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="activity_activity_comments">
            @includeIf('admin.activities.relationships.activityActivityComments', ['activityComments' => $activity->activityActivityComments])
        </div>
    </div>
</div>

@endsection
