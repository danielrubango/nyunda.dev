<?php

namespace App\Actions\Forum;

use App\Models\ForumThread;

class UpdateForumThread
{
    /**
     * @param  array{locale: string, title: string, body_markdown: string}  $payload
     */
    public function handle(ForumThread $forumThread, array $payload): ForumThread
    {
        $forumThread->update([
            'locale' => (string) $payload['locale'],
            'title' => (string) $payload['title'],
            'body_markdown' => (string) $payload['body_markdown'],
        ]);

        return $forumThread->refresh();
    }
}
