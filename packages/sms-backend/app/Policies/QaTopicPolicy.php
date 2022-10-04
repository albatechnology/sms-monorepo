<?php

namespace App\Policies;

use App\Models\QaTopic;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QaTopicPolicy
{
    use HandlesAuthorization;

    public function __construct()
    {
        //
    }

    public function show(User $user, QaTopic $qaTopic)
    {
        return ($user->is_admin || $qaTopic->isInvolved($user))
            ? $this->allow()
            : $this->deny('Only creator and subscribers can view this topic');
    }

    public function destroy(User $user, QaTopic $qaTopic)
    {
        return $user->is_admin || $user->id === $qaTopic->creator_id
            ? $this->allow()
            : $this->deny('You do not own this QaTopic.');
    }

    public function update(User $user, QaTopic $qaTopic)
    {
        return $user->is_admin || $user->id === $qaTopic->creator_id
            ? $this->allow()
            : $this->deny('You do not own this QaTopic.');
    }
}