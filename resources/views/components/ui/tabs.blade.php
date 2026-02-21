@props([
    'items' => [],
])

<nav {{ $attributes->class(['inline-flex max-w-full items-center gap-2 overflow-x-auto rounded-xl border border-zinc-200 bg-white p-1']) }} aria-label="{{ __('ui.accessibility.tabs') }}">
    @foreach ($items as $item)
        <a
            href="{{ $item['url'] }}"
            @class([
                'rounded-lg px-3 py-1.5 text-sm font-medium no-underline whitespace-nowrap transition-colors',
                'bg-zinc-900 text-white hover:text-white' => (bool) ($item['active'] ?? false),
                'text-zinc-700 hover:bg-zinc-100' => ! (bool) ($item['active'] ?? false),
            ])
            @if (! empty($item['ariaCurrent']))
                aria-current="{{ $item['ariaCurrent'] }}"
            @endif
        >
            {{ $item['label'] }}
        </a>
    @endforeach
</nav>
