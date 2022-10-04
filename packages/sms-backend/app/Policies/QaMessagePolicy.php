<?php

namespace App\Policies;

use App\Models\QaMessage;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QaMessagePolicy
{
    use HandlesAuthorization;

    public function show(User $user, QaMessage $qaMessage)
    {
        return (new QaTopicPolicy())->show($user, $qaMessage->topic);
    }

    public function destroy(User $user, QaMessage $qaMessage)
    {
        return $user->is_admin
            ? $this->allow()
            : $this->deny('Only admin can remove messages');
    }

    public function store(User $user, QaMessage $qaMessage)
    {
        return $user->is_admin || $qaMessage->topic->isInvolved($user)
            ? $this->allow()
            : $this->deny('You not part of this conversation');
    }

    public function update(User $user, QaMessage $qaMessage)
    {
        return $user->is_admin || $user->id === $qaMessage->sender_id
            ? $this->allow()
            : $this->deny('You can only update your own message');
    }
}