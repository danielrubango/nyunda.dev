<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function update(User $user, Comment $comment): bool
    {
        return $user->hasRole(UserRole::Admin);
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $user->hasRole(UserRole::Admin);
    }
}
