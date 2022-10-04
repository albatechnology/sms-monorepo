<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\QaTopicCreateRequest;
use App\Http\Requests\QaTopicReplyRequest;
use App\Models\QaMessageUser;
use App\Models\QaTopic;
use App\Models\User;
use Auth;
use Carbon\Carbon;

class MessengerController extends Controller
{
    public function index()
    {
        $topics = QaTopic::involved()
                         ->orderBy('created_at', 'DESC')
                         ->get();

        $title   = trans('global.all_messages');
        $unreads = $this->unreadTopics();

        return view('admin.messenger.index', compact('topics', 'title', 'unreads'));
    }

    public function createTopic()
    {
        $users = User::all()->except(Auth::id());

        $unreads = $this->unreadTopics();

        return view('admin.messenger.create', compact('users', 'unreads'));
    }

    public function storeTopic(QaTopicCreateRequest $request)
    {
        $topic = QaTopic::create([
            'subject'    => $request->input('subject'),
            'creator_id' => user()->id,
        ]);

        $topic->users()->attach([user()->id, $request->input('recipient')]);

        $topic->messages()->create([
            'sender_id' => user()->id,
            'content'   => $request->input('content'),
        ]);

        return redirect()->route('admin.messenger.index');
    }

    public function showMessages(QaTopic $topic)
    {
        $this->checkAccessRights($topic);

        foreach ($topic->messages as $message) {
            if ($message->sender_id !== Auth::id() && $message->read_at === null) {
                $message->read_at = Carbon::now();
                $message->save();
            }
        }

        $unreads = $this->unreadTopics();

        return view('admin.messenger.show', compact('topic', 'unreads'));
    }

    public function destroyTopic(QaTopic $topic)
    {
        $this->checkAccessRights($topic);

        $topic->delete();

        return redirect()->route('admin.messenger.index');
    }

    public function showInbox()
    {
        $title = trans('global.inbox');

        $topics = QaTopic::whereHas('subsribers', function ($query) {
            $query->where('user_id', user()->id);
        })
                         ->orderBy('created_at', 'DESC')
                         ->get();

        // filter out topic created by this user
        $topics = $topics->filter(fn(QaTopic $q) => $q->user_id != user()->id);

        $unreads = $this->unreadTopics();

        return view('admin.messenger.index', compact('topics', 'title', 'unreads'));
    }

    public function showOutbox()
    {
        $title = trans('global.outbox');

        $topics = QaTopic::where('creator_id', user()->id)
                         ->orderBy('created_at', 'DESC')
                         ->get();

        $unreads = $this->unreadTopics();

        return view('admin.messenger.index', compact('topics', 'title', 'unreads'));
    }

    public function replyToTopic(QaTopicReplyRequest $request, QaTopic $topic)
    {
        $this->checkAccessRights($topic);

        $topic->messages()->create([
            'sender_id' => Auth::id(),
            'content'   => $request->input('content'),
        ]);

        return redirect()->route('admin.messenger.index');
    }

    public function showReply(QaTopic $topic)
    {
        $this->checkAccessRights($topic);

        $unreads = $this->unreadTopics();

        return view('admin.messenger.reply', compact('topic', 'unreads'));
    }

    public function unreadTopics(): array
    {
        $unreads         = QaMessageUser::where('user_id', user()->id)->get();
        $owned_topic_ids = user()->qaTopics()->get('id')->pluck('id');

        $outboxUnreadCount = $unreads->count(fn($q) => $owned_topic_ids->contains($q->topic_id));
        $inboxUnreadCount  = $unreads->count() - $outboxUnreadCount;

        return [
            'inbox'  => $inboxUnreadCount,
            'outbox' => $outboxUnreadCount,
        ];
    }

    private function checkAccessRights(QaTopic $topic)
    {
        if (!$topic->isInvolved(user())) {
            abort(401);
        }
    }
}
