<?php

namespace App\Actions\Forum;

use App\Models\ForumThread;
use App\Models\User;
use Illuminate\Support\Str;

class CreateForumThread
{
    /**
     * @param  array{locale: string, title: string, body_markdown: string}  $payload
     */
    public function handle(User $author, array $payload): ForumThread
    {
        $slug = $this->buildUniqueSlug((string) $payload['title']);

        return ForumThread::query()->create([
            'author_id' => $author->id,
            'locale' => (string) $payload['locale'],
            'title' => (string) $payload['title'],
            'slug' => $slug,
            'body_markdown' => (string) $payload['body_markdown'],
        ]);
    }

    protected function buildUniqueSlug(string $title): string
    {
        $baseSlug = Str::slug($title);
        $baseSlug = $baseSlug === '' ? 'thread' : $baseSlug;
        $slug = $baseSlug;
        $counter = 2;

        while (ForumThread::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
