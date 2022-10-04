@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.activityComment.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.activity-comments.update", [$activityComment->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label for="content">{{ trans('cruds.activityComment.fields.content') }}</label>
                <textarea class="form-control {{ $errors->has('content') ? 'is-invalid' : '' }}" name="content" id="content">{{ old('content', $activityComment->content) }}</textarea>
                @if($errors->has('content'))
                    <span class="text-danger">{{ $errors->first('content') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.activityComment.fields.content_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="user_id">{{ trans('cruds.activityComment.fields.user') }}</label>
                <select class="form-control select2 {{ $errors->has('user') ? 'is-invalid' : '' }}" name="user_id" id="user_id">
                    @foreach($users as $id => $user)
                        <option value="{{ $id }}" {{ (old('user_id') ? old('user_id') : $activityComment->user->id ?? '') == $id ? 'selected' : '' }}>{{ $user }}</option>
                    @endforeach
                </select>
                @if($errors->has('user'))
                    <span class="text-danger">{{ $errors->first('user') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.activityComment.fields.user_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="activity_id">{{ trans('cruds.activityComment.fields.activity') }}</label>
                <select class="form-control select2 {{ $errors->has('activity') ? 'is-invalid' : '' }}" name="activity_id" id="activity_id">
                    @foreach($activities as $id => $activity)
                        <option value="{{ $id }}" {{ (old('activity_id') ? old('activity_id') : $activityComment->activity->id ?? '') == $id ? 'selected' : '' }}>{{ $activity }}</option>
                    @endforeach
                </select>
                @if($errors->has('activity'))
                    <span class="text-danger">{{ $errors->first('activity') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.activityComment.fields.activity_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="activity_comment_id">{{ trans('cruds.activityComment.fields.activity_comment') }}</label>
                <select class="form-control select2 {{ $errors->has('activity_comment') ? 'is-invalid' : '' }}" name="activity_comment_id" id="activity_comment_id">
                    @foreach($activity_comments as $id => $activity_comment)
                        <option value="{{ $id }}" {{ (old('activity_comment_id') ? old('activity_comment_id') : $activityComment->activity_comment->id ?? '') == $id ? 'selected' : '' }}>{{ $activity_comment }}</option>
                    @endforeach
                </select>
                @if($errors->has('activity_comment'))
                    <span class="text-danger">{{ $errors->first('activity_comment') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.activityComment.fields.activity_comment_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection