<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\ForumReply;
use App\Models\User;

class ForumReplyPolicy
{
    public function create(User $user): bool
    {
        return $user !== null;
    }

    public function update(User $user, ForumReply $forumReply): bool
    {
        return $user->hasRole(UserRole::Admin);
    }

    public function delete(User $user, ForumReply $forumReply): bool
    {
        return $user->hasRole(UserRole::Admin) || $forumReply->user_id === $user->id;
    }

    public function moderate(User $user, ForumReply $forumReply): bool
    {
        return $user->hasRole(UserRole::Admin);
    }
}
