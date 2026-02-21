<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(UserRole::Admin);
    }

    public function view(User $user, Comment $comment): bool
    {
        return $user->hasRole(UserRole::Admin);
    }

    public function update(User $user, Comment $comment): bool
    {
        return $user->hasRole(UserRole::Admin);
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $user->hasRole(UserRole::Admin);
    }
}
