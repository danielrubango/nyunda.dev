<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\ForumThread;
use App\Models\User;

class ForumThreadPolicy
{
    public function create(User $user): bool
    {
        return $user !== null;
    }

    public function update(User $user, ForumThread $forumThread): bool
    {
        return $user->hasRole(UserRole::Admin) || $forumThread->author_id === $user->id;
    }

    public function delete(User $user, ForumThread $forumThread): bool
    {
        return $user->hasRole(UserRole::Admin) || $forumThread->author_id === $user->id;
    }

    public function markBestReply(User $user, ForumThread $forumThread): bool
    {
        return $user->hasRole(UserRole::Admin) || $forumThread->author_id === $user->id;
    }
}
