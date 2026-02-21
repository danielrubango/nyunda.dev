<div class="flex items-center gap-2">
    <button
        type="button"
        wire:click="toggleLike"
        wire:target="toggleLike"
        wire:loading.attr="disabled"
        class="inline-flex size-9 cursor-pointer items-center justify-center border bg-white transition-colors hover:bg-zinc-100 {{ $liked ? 'border-zinc-600 text-red-600 hover:border-zinc-700' : 'border-zinc-300 text-zinc-500 hover:border-zinc-500 hover:text-zinc-700' }}"
        aria-pressed="{{ $liked ? 'true' : 'false' }}"
        title="{{ __('ui.blog.like_toggle') }}"
    >
        <x-ui.icon name="heart" class="size-4" />
        <span class="sr-only">{{ __('ui.blog.like_toggle') }}</span>
    </button>

    <span class="text-sm font-medium text-zinc-700">{{ $likesCount }}</span>
</div>
