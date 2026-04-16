<div class="flex flex-col gap-3" data-test="oauth-buttons">
    <div class="relative text-center">
        <span class="bg-white px-3 text-xs font-medium tracking-wide text-zinc-500">{{ __('ui.auth.social.divider') }}</span>
    </div>

    <div class="grid gap-3 sm:grid-cols-2">
        <a
            href="{{ route('oauth.redirect', ['provider' => 'google']) }}"
            class="inline-flex h-10 items-center justify-center gap-2 rounded-md border border-zinc-300 px-4 text-sm font-medium text-zinc-800 no-underline transition-colors hover:bg-zinc-100"
            data-test="oauth-google-button"
        >
            <x-ui.icon name="google" class="size-4" />
            <span>{{ __('ui.auth.social.google') }}</span>
        </a>

        <a
            href="{{ route('oauth.redirect', ['provider' => 'linkedin']) }}"
            class="inline-flex h-10 items-center justify-center gap-2 rounded-md border border-zinc-300 px-4 text-sm font-medium text-zinc-800 no-underline transition-colors hover:bg-zinc-100"
            data-test="oauth-linkedin-button"
        >
            <x-ui.icon name="linkedin" class="size-4" />
            <span>{{ __('ui.auth.social.linkedin') }}</span>
        </a>
    </div>
</div>
