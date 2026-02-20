<?php

namespace App\Actions\Content;

use Illuminate\Support\Str;

class RenderSafeMarkdown
{
    public function handle(?string $markdown): string
    {
        if ($markdown === null || $markdown === '') {
            return '';
        }

        return (string) Str::markdown($markdown, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }
}
