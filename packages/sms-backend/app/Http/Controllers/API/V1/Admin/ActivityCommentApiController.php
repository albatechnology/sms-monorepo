<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreActivityCommentRequest;
use App\Http\Requests\UpdateActivityCommentRequest;
use App\Http\Resources\Admin\ActivityCommentResource;
use App\Models\ActivityComment;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActivityCommentApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('activity_comment_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ActivityCommentResource(ActivityComment::with(['user', 'activity', 'activity_comment'])->get());
    }

    public function store(StoreActivityCommentRequest $request)
    {
        $activityComment = ActivityComment::create($request->all());

        return (new ActivityCommentResource($activityComment))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(ActivityComment $activityComment)
    {
        abort_if(Gate::denies('activity_comment_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ActivityCommentResource($activityComment->load(['user', 'activity', 'activity_comment']));
    }

    public function update(UpdateActivityCommentRequest $request, ActivityComment $activityComment)
    {
        $activityComment->update($request->all());

        return (new ActivityCommentResource($activityComment))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(ActivityComment $activityComment)
    {
        abort_if(Gate::denies('activity_comment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $activityComment->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
