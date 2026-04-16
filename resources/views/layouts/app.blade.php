<x-layouts.public :seo="array_filter([
    'title' => $title ?? null,
    'robots' => 'noindex,follow',
], fn (mixed $value): bool => $value !== null)">
    <div class="ui-container">
        <div class="mx-auto w-full max-w-4xl">
            {{ $slot }}
        </div>
    </div>
</x-layouts.public>
