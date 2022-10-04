<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyActivityCommentRequest;
use App\Http\Requests\StoreActivityCommentRequest;
use App\Http\Requests\UpdateActivityCommentRequest;
use App\Models\Activity;
use App\Models\ActivityComment;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ActivityCommentController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('activity_comment_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ActivityComment::with(['user', 'activity', 'activity_comment'])->select(sprintf('%s.*', (new ActivityComment)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'activity_comment_show';
                $editGate      = 'activity_comment_edit';
                $deleteGate    = 'activity_comment_delete';
                $crudRoutePart = 'activity-comments';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : "";
            });
            $table->editColumn('content', function ($row) {
                return $row->content ? $row->content : "";
            });
            $table->addColumn('user_name', function ($row) {
                return $row->user ? $row->user->name : '';
            });

            $table->addColumn('activity_follow_up_datetime', function ($row) {
                return $row->activity ? $row->activity->follow_up_datetime : '';
            });

            $table->addColumn('activity_comment_content', function ($row) {
                return $row->activity_comment ? $row->activity_comment->content : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'user', 'activity', 'activity_comment']);

            return $table->make(true);
        }

        $users             = User::get();
        $activities        = Activity::get();
        $activity_comments = ActivityComment::get();

        return view('admin.activityComments.index', compact('users', 'activities', 'activity_comments'));
    }

    public function create()
    {
        abort_if(Gate::denies('activity_comment_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $activities = Activity::all()->pluck('follow_up_datetime', 'id')->prepend(trans('global.pleaseSelect'), '');

        $activity_comments = ActivityComment::all()->pluck('content', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.activityComments.create', compact('users', 'activities', 'activity_comments'));
    }

    public function store(StoreActivityCommentRequest $request)
    {
        $activityComment = ActivityComment::create($request->validated());

        return redirect()->route('admin.activity-comments.index');
    }

    public function edit(ActivityComment $activityComment)
    {
        abort_if(Gate::denies('activity_comment_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $activities = Activity::all()->pluck('follow_up_datetime', 'id')->prepend(trans('global.pleaseSelect'), '');

        $activity_comments = ActivityComment::all()->pluck('content', 'id')->prepend(trans('global.pleaseSelect'), '');

        $activityComment->load('user', 'activity', 'activity_comment');

        return view('admin.activityComments.edit', compact('users', 'activities', 'activity_comments', 'activityComment'));
    }

    public function update(UpdateActivityCommentRequest $request, ActivityComment $activityComment)
    {
        $activityComment->update($request->validated());

        return redirect()->route('admin.activity-comments.index');
    }

    public function show(ActivityComment $activityComment)
    {
        abort_if(Gate::denies('activity_comment_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $activityComment->load('user', 'activity', 'activity_comment', 'activityCommentActivityComments');

        return view('admin.activityComments.show', compact('activityComment'));
    }

    public function destroy(ActivityComment $activityComment)
    {
        abort_if(Gate::denies('activity_comment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $activityComment->delete();

        return back();
    }

    public function massDestroy(MassDestroyActivityCommentRequest $request)
    {
        ActivityComment::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
