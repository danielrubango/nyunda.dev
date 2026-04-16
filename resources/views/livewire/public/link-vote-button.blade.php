<div class="flex items-center gap-2">
    <button
        type="button"
        wire:click="toggleVote"
        wire:target="toggleVote"
        wire:loading.attr="disabled"
        class="inline-flex size-9 cursor-pointer items-center justify-center border bg-white transition-colors hover:bg-zinc-100 {{ $voted ? 'border-zinc-600 text-amber-600 hover:border-zinc-700' : 'border-zinc-300 text-zinc-400 hover:border-zinc-500 hover:text-zinc-600' }}"
        aria-pressed="{{ $voted ? 'true' : 'false' }}"
        title="{{ __('ui.links.vote_toggle') }}"
    >
        <x-ui.icon name="arrow-up" class="size-4" />
        <span class="sr-only">{{ __('ui.links.vote_toggle') }}</span>
    </button>

    <span class="text-sm font-medium text-zinc-700">{{ $votesCount }}</span>
</div>
