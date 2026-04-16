<?php

namespace App\Support;

use Illuminate\Support\Str;

class SeoDescription
{
    public function forMeta(
        ?string $description,
        ?string $fallback = null,
        ?string $title = null,
        int $maxLength = 160,
        int $minimumLength = 70,
    ): string {
        $primary = $this->preparePlainText($description, $title);

        if (! $this->isWeak($primary, $minimumLength)) {
            return $this->trimAtWordBoundary($primary, $maxLength);
        }

        $fallbackText = $this->preparePlainText($fallback, $title);

        if ($fallbackText !== '') {
            return $this->trimAtWordBoundary($fallbackText, $maxLength);
        }

        return $this->trimAtWordBoundary($primary !== '' ? $primary : (string) $title, $maxLength);
    }

    public function forMarkdownMeta(
        ?string $markdown,
        ?string $fallback = null,
        ?string $title = null,
        int $maxLength = 160,
        int $minimumLength = 70,
    ): string {
        return $this->forMeta(
            description: $this->markdownToText($markdown, $title),
            fallback: $fallback,
            title: $title,
            maxLength: $maxLength,
            minimumLength: $minimumLength,
        );
    }

    public function generateExcerpt(?string $source, ?string $title = null, int $maxLength = 200): string
    {
        return $this->trimAtWordBoundary(
            $this->markdownToText($source, $title),
            $maxLength,
        );
    }

    public function generatePlainExcerpt(?string $source, ?string $title = null, int $maxLength = 200): string
    {
        return $this->trimAtWordBoundary(
            $this->preparePlainText($source, $title),
            $maxLength,
        );
    }

    public function isWeak(?string $text, int $minimumLength = 70): bool
    {
        return mb_strlen(trim((string) $text)) < $minimumLength;
    }

    protected function markdownToText(?string $markdown, ?string $title = null): string
    {
        return $this->preparePlainText(
            strip_tags((string) Str::markdown((string) $markdown)),
            $title,
        );
    }

    protected function preparePlainText(?string $text, ?string $title = null): string
    {
        $plainText = html_entity_decode((string) $text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        $plainText = (string) Str::of($plainText)
            ->replaceMatches('/!\[[^\]]*\]\([^)]+\)/u', ' ')
            ->replaceMatches('/\[[^\]]+\]\([^)]+\)/u', ' ')
            ->replaceMatches('/[`*_>#-]+/u', ' ')
            ->replace(["\r\n", "\r", "\n", "\t"], ' ')
            ->squish();

        if (! is_string($title) || trim($title) === '') {
            return trim($plainText);
        }

        $escapedTitle = preg_quote(trim($title), '/');
        $withoutTitle = preg_replace(
            "/^{$escapedTitle}(?:\\s*[-|:,.!?]+\\s*|\\s+)/iu",
            '',
            trim($plainText),
        );

        return trim($withoutTitle ?: trim($plainText));
    }

    protected function trimAtWordBoundary(string $text, int $maxLength): string
    {
        $trimmed = trim($text);

        if ($trimmed === '' || mb_strlen($trimmed) <= $maxLength) {
            return $trimmed;
        }

        $candidate = mb_substr($trimmed, 0, $maxLength + 1);
        $lastSpacePosition = mb_strrpos($candidate, ' ');

        if ($lastSpacePosition !== false && $lastSpacePosition >= (int) floor($maxLength * 0.7)) {
            $candidate = mb_substr($candidate, 0, $lastSpacePosition);
        } else {
            $candidate = mb_substr($candidate, 0, $maxLength);
        }

        return rtrim($candidate, " \t\n\r\0\x0B.,;:-");
    }
}
