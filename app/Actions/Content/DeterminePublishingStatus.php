<?php

namespace App\Actions\Content;

use App\Enums\ContentStatus;
use App\Models\User;

class DeterminePublishingStatus
{
    public function handle(User $author): ContentStatus
    {
        return $author->canPublishWithoutApproval()
            ? ContentStatus::Published
            : ContentStatus::Pending;
    }
}
