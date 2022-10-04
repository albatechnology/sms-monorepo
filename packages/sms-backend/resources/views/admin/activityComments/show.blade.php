@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.activityComment.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.activity-comments.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.activityComment.fields.id') }}
                        </th>
                        <td>
                            {{ $activityComment->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.activityComment.fields.content') }}
                        </th>
                        <td>
                            {{ $activityComment->content }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.activityComment.fields.user') }}
                        </th>
                        <td>
                            {{ $activityComment->user->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.activityComment.fields.activity') }}
                        </th>
                        <td>
                            {{ $activityComment->activity->follow_up_datetime ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.activityComment.fields.activity_comment') }}
                        </th>
                        <td>
                            {{ $activityComment->activity_comment->content ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.activity-comments.index') }}">
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
            <a class="nav-link" href="#activity_comment_activity_comments" role="tab" data-toggle="tab">
                {{ trans('cruds.activityComment.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="activity_comment_activity_comments">
            @includeIf('admin.activityComments.relationships.activityCommentActivityComments', ['activityComments' => $activityComment->activityCommentActivityComments])
        </div>
    </div>
</div>

@endsection