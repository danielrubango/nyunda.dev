@props([
    'item',
    'translation',
    'showDescription' => false,
])

@php
    $sharedAt = $item->published_at?->format('Y-m-d') ?? $item->created_at?->format('Y-m-d');
    $host = parse_url((string) $translation->external_url, PHP_URL_HOST);
    $domain = is_string($host) ? \Illuminate\Support\Str::of($host)->replaceStart('www.', '')->value() : null;
    $description = $translation->external_description ?: $translation->excerpt;
    $isExternalPost = $item->type === \App\Enums\ContentType::ExternalPost;
    $cardBorderClasses = $isExternalPost ? 'border-brand-300' : 'border-zinc-300';
    $cardHoverBackgroundClasses = $isExternalPost ? 'hover:bg-brand-50' : 'hover:bg-zinc-100';
@endphp

<x-ui.card
    as="a"
    :href="$translation->external_url"
    target="_blank"
    rel="noopener noreferrer"
    class="group relative flex h-full flex-col no-underline transition-colors {{ $cardBorderClasses }} {{ $cardHoverBackgroundClasses }}"
    data-testid="external-link-card"
>
    <x-ui.icon
        name="external-link"
        class="pointer-events-none absolute top-5 right-5 size-4 shrink-0 opacity-0 transition-opacity duration-150 group-hover:opacity-100"
    />

    <div class="flex grow flex-col gap-4">
        <h3 class="pr-8 font-sans text-xl font-semibold tracking-tight text-zinc-900 transition-colors group-hover:text-brand-700">
            {{ $translation->title }}
        </h3>

        @if ($showDescription)
            <p class="text-sm text-zinc-600">{{ $description }}</p>
        @endif

        <div class="mt-auto flex items-end justify-between gap-3">
            <p class="text-xs text-zinc-500">
                {{ __('ui.links.shared_on_domain', ['date' => $sharedAt, 'domain' => $domain ?? __('ui.links.unknown_domain')]) }}
            </p>

            <div class="flex shrink-0 items-center gap-2">
                <x-ui.badge :variant="$isExternalPost ? 'external' : 'community'">
                    {{ __('ui.blog.content_types.'.$item->type->value) }}
                </x-ui.badge>
                <x-ui.badge>{{ strtoupper($translation->locale) }}</x-ui.badge>
            </div>
        </div>
    </div>
</x-ui.card>
