@php
    $providers = collect([
        [
            'name' => 'google',
            'label' => __('ui.auth.social.google'),
            'icon' => 'google',
            'enabled' => (bool) config('services.google.enabled'),
        ],
        [
            'name' => 'linkedin',
            'label' => __('ui.auth.social.linkedin'),
            'icon' => 'linkedin',
            'enabled' => (bool) config('services.linkedin-openid.enabled'),
        ],
    ])->filter(fn (array $provider): bool => $provider['enabled'])->values();
@endphp

@if ($providers->isNotEmpty())
    <div class="flex flex-col gap-4" data-test="oauth-buttons">
        <div class="text-center">
            <p class="text-sm font-medium text-zinc-500">{{ __('ui.auth.social.heading') }}</p>
        </div>

        <div @class([
            'grid gap-3',
            'sm:grid-cols-2' => $providers->count() > 1,
        ])>
            @foreach ($providers as $provider)
                <a
                    href="{{ route('oauth.redirect', ['provider' => $provider['name']]) }}"
                    class="inline-flex h-10 items-center justify-center gap-2 rounded-md border border-zinc-300 px-4 text-sm font-medium text-zinc-800 no-underline transition-colors hover:bg-zinc-100"
                    data-test="oauth-{{ $provider['name'] }}-button"
                >
                    <x-ui.icon :name="$provider['icon']" class="size-4" />
                    <span>{{ $provider['label'] }}</span>
                </a>
            @endforeach
        </div>

        <div class="flex items-center gap-3" data-test="auth-methods-separator">
            <div class="h-px flex-1 bg-zinc-200"></div>
            <span class="text-xs font-medium uppercase tracking-wide text-zinc-500">{{ __('ui.auth.social.separator') }}</span>
            <div class="h-px flex-1 bg-zinc-200"></div>
        </div>
    </div>
@endif
