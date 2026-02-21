@props([
    'item',
    'translation',
    'size' => 'md',
])

@php
    $isInternal = $item->type === \App\Enums\ContentType::InternalPost;
    $isExternal = $item->type === \App\Enums\ContentType::ExternalPost;

    $href = $isInternal
        ? route('blog.show', ['locale' => $translation->locale, 'slug' => $translation->slug])
        : $translation->external_url;

    $publishedAt = $item->published_at?->format('Y-m-d') ?? $item->created_at?->format('Y-m-d');
    $authorName = $item->author?->name ?? config('app.name');

    $externalHost = parse_url((string) $translation->external_url, PHP_URL_HOST);
    $externalDomain = is_string($externalHost) ? \Illuminate\Support\Str::of($externalHost)->replaceStart('www.', '')->value() : null;

    $excerpt = trim((string) $translation->excerpt);

    if ($isExternal && is_string($translation->external_url) && $translation->external_url !== '') {
        $shortExternalUrl = \Illuminate\Support\Str::limit($translation->external_url, 68);
        $excerpt = $excerpt === '' ? $shortExternalUrl : trim($excerpt.' — '.$shortExternalUrl);
    }

    $titleClasses = match ($size) {
        'xl' => 'text-3xl sm:text-4xl',
        'lg' => 'text-2xl sm:text-3xl',
        default => 'text-xl sm:text-2xl',
    };
    $titlePaddingClasses = $isExternal ? 'pr-8' : '';

    $cardBorderClasses = $isInternal
        ? 'border-zinc-300'
        : 'border-brand-300';
    $cardHoverBackgroundClasses = $isInternal
        ? 'hover:bg-zinc-100'
        : 'hover:bg-brand-50';
@endphp

<x-ui.card
    as="a"
    :href="$href"
    :target="$isExternal ? '_blank' : null"
    :rel="$isExternal ? 'noopener noreferrer' : null"
    {{ $attributes->class([
        'group relative flex h-full flex-col no-underline transition-colors',
        $cardBorderClasses,
        $cardHoverBackgroundClasses,
    ]) }}
>
    @if ($isExternal)
        <x-ui.icon
            name="external-link"
            class="pointer-events-none absolute top-5 right-5 size-4 shrink-0 opacity-0 transition-opacity duration-150 group-hover:opacity-100"
        />
    @endif

    <div class="flex grow flex-col gap-4">
        <h3 class="{{ $titleClasses }} {{ $titlePaddingClasses }} font-semibold tracking-tight text-zinc-900 transition-colors group-hover:text-brand-700">
            {{ $translation->title }}
        </h3>

        <p class="line-clamp-3 text-sm text-zinc-600">{{ $excerpt }}</p>

        <div class="mt-auto flex items-end justify-between gap-3">
            <p class="text-xs text-zinc-500">
                @if ($isInternal)
                    {{ __('ui.blog.published_by', ['date' => $publishedAt, 'author' => $authorName]) }}
                @else
                    {{ __('ui.blog.shared_on_domain', ['date' => $publishedAt, 'domain' => $externalDomain ?? __('ui.links.unknown_domain')]) }}
                @endif
            </p>

            <div class="flex shrink-0 items-center gap-2">
                <x-ui.badge :variant="$isInternal ? 'internal' : 'external'">
                    {{ __('ui.blog.content_types.'.$item->type->value) }}
                </x-ui.badge>
                <x-ui.badge>{{ strtoupper($translation->locale) }}</x-ui.badge>
            </div>
        </div>
    </div>
</x-ui.card>
