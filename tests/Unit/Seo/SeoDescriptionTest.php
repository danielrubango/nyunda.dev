<?php

use App\Support\SeoDescription;

test('it strips markdown and trims on a word boundary', function () {
    $description = app(SeoDescription::class)->generateExcerpt(
        "# Heading\n\nThis paragraph explains how to keep a Laravel SEO description clean and focused without ending in the middle of a sentence.",
        'Heading',
        100,
    );

    expect($description)
        ->not->toContain('#')
        ->toStartWith('This paragraph explains')
        ->not->toEndWith('a');
});

test('it falls back to stronger content when the excerpt is too weak', function () {
    $description = app(SeoDescription::class)->forMeta(
        description: 'Short excerpt',
        fallback: 'Fallback text that is long enough to become the final meta description for this article page.',
        title: 'Article title',
    );

    expect($description)
        ->toContain('Fallback text')
        ->not->toContain('Short excerpt');
});

test('it removes a duplicated title prefix from the generated description', function () {
    $description = app(SeoDescription::class)->forMarkdownMeta(
        markdown: "# Article title\n\nArticle title: this Laravel post explains how to align meta descriptions with the actual content shown on the page.",
        title: 'Article title',
    );

    expect($description)
        ->toStartWith('this Laravel post explains')
        ->not->toContain('# Article title');
});
