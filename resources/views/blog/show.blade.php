<x-layouts.public :seo="$seo">
    <div class="ui-container">
        <div class="mx-auto -mt-5 max-w-3xl space-y-6 sm:-mt-6">
            <a href="{{ route('blog.index') }}" class="inline-flex text-sm font-medium text-zinc-600 no-underline hover:text-brand-700">
                {{ __('ui.blog.back') }}
            </a>

            <header class="space-y-3">
                @if ($translation->featured_image_url)
                    <img
                        src="{{ $translation->featured_image_url }}"
                        alt="{{ $translation->title }}"
                        width="1200"
                        height="675"
                        loading="eager"
                        class="aspect-video w-full border border-zinc-200 object-cover"
                    >
                @endif

                <div class="flex flex-wrap items-center gap-2">
                    <x-ui.badge variant="internal">{{ __('ui.blog.content_types.internal_post') }}</x-ui.badge>
                    <span class="text-xs font-medium uppercase tracking-wide text-zinc-500">{{ strtoupper($translation->locale) }}</span>
                </div>

                <h1 class="font-sans text-4xl font-semibold tracking-tight text-zinc-900 sm:text-5xl">{{ $translation->title }}</h1>
                <p class="flex flex-wrap items-center gap-2 text-sm text-zinc-500">
                    <span>
                        {{ __('ui.blog.published_by', [
                            'date' => $contentItem->published_at?->format('Y-m-d') ?? $contentItem->created_at?->format('Y-m-d'),
                            'author' => $contentItem->author->name,
                        ]) }}
                    </span>
                    @if ($isAdmin)
                        <span class="inline-flex items-center gap-1 font-medium text-zinc-500">
                            <x-ui.icon name="eye" class="size-4" />
                            <span>{{ number_format((int) $contentItem->reads_count) }}</span>
                            <span class="sr-only">{{ __('ui.blog.reads', ['count' => (int) $contentItem->reads_count]) }}</span>
                        </span>
                    @endif
                </p>
            </header>

            <article class="article-content">
                {!! $renderedBody !!}
            </article>

            <section class="flex flex-wrap items-center gap-4 border-y border-zinc-200 py-4">
                @if ($contentItem->show_likes)
                    @auth
                        <livewire:public.content-like-button
                            :content-item="$contentItem"
                            :liked="$hasLiked"
                            :key="'content-like-'.$contentItem->id"
                        />
                    @else
                        <div class="flex items-center gap-2">
                            <form method="POST" action="{{ route('content.likes.toggle', ['contentItem' => $contentItem]) }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="inline-flex size-9 cursor-pointer items-center justify-center border border-zinc-300 text-zinc-500 transition-colors hover:border-zinc-500 hover:text-zinc-700"
                                    title="{{ __('ui.blog.like_toggle') }}"
                                >
                                    <x-ui.icon name="heart" class="size-4" />
                                    <span class="sr-only">{{ __('ui.blog.like_toggle') }}</span>
                                </button>
                            </form>
                            <span class="text-sm font-medium text-zinc-700">{{ (int) $contentItem->likes_count }}</span>
                        </div>
                    @endauth
                @endif

                <div class="ms-auto">
                    <details class="relative">
                        <summary class="inline-flex h-9 cursor-pointer list-none items-center gap-2 border border-zinc-300 px-3 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100">
                            <x-ui.icon name="share" class="size-4" />
                            <span>{{ __('ui.blog.share.menu') }}</span>
                        </summary>
                        <div class="absolute right-0 top-11 z-20 min-w-52 border border-zinc-200 bg-white p-2 text-sm shadow-xs">
                            <a
                                href="{{ 'https://twitter.com/intent/tweet?url='.urlencode(request()->fullUrl()).'&text='.urlencode($translation->title) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex items-center gap-2 px-2 py-1.5 text-zinc-700 no-underline transition-colors hover:bg-zinc-100 hover:text-zinc-900"
                            >
                                <x-ui.icon name="x" class="size-4" />
                                <span>{{ __('ui.blog.share.x') }}</span>
                            </a>
                            <a
                                href="{{ 'https://www.linkedin.com/sharing/share-offsite/?url='.urlencode(request()->fullUrl()) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex items-center gap-2 px-2 py-1.5 text-zinc-700 no-underline transition-colors hover:bg-zinc-100 hover:text-zinc-900"
                            >
                                <x-ui.icon name="linkedin" class="size-4" />
                                <span>{{ __('ui.blog.share.linkedin') }}</span>
                            </a>
                            <button
                                type="button"
                                data-copy-link="{{ request()->fullUrl() }}"
                                data-label-default="{{ __('ui.blog.share.copy') }}"
                                data-label-success="{{ __('ui.blog.share.copied') }}"
                                data-label-error="{{ __('ui.blog.share.copy_error') }}"
                                class="flex w-full items-center gap-2 px-2 py-1.5 text-start text-zinc-700 transition-colors hover:bg-zinc-100 hover:text-zinc-900"
                            >
                                <x-ui.icon name="copy" class="size-4" />
                                <span data-copy-label>{{ __('ui.blog.share.copy') }}</span>
                            </button>
                        </div>
                    </details>
                </div>
            </section>

            @if ($contentItem->show_comments)
                <section
                    class="space-y-4"
                    x-data="commentSection({
                        deletedToast: @js(__('ui.flash.comment_deleted')),
                        failedToast: @js(__('ui.flash.action_failed')),
                    })"
                >
                    <h2 class="ui-section-title">{{ __('ui.blog.comments.title') }}</h2>

                    <x-ui.card :padding="false">
                        @if ($comments->isEmpty())
                            <p class="px-5 py-6 text-sm text-zinc-500 sm:px-6">{{ __('ui.blog.comments.empty') }}</p>
                        @else
                            <div class="divide-y divide-zinc-300">
                            @foreach ($comments as $comment)
                                @php
                                    $commentPublishedAt = $comment->created_at;
                                    $commentPublishedLabel = '';
                                    $commentDeleteModal = 'confirm-comment-deletion-'.$comment->id;

                                    if ($commentPublishedAt !== null) {
                                        $commentPublishedLabel = $commentPublishedAt->diffInSeconds(now()) < 60
                                            ? __('ui.blog.comments.just_now')
                                            : $commentPublishedAt->locale(app()->getLocale())->diffForHumans();
                                    }
                                @endphp
                                <article
                                    x-data="commentActions({
                                        hidden: @js((bool) $comment->is_hidden),
                                        id: @js($comment->id),
                                        hiddenLabel: @js(__('ui.blog.comments.hide')),
                                        shownLabel: @js(__('ui.blog.comments.show')),
                                        hiddenToast: @js(__('ui.flash.comment_hidden')),
                                        shownToast: @js(__('ui.flash.comment_shown')),
                                        failedToast: @js(__('ui.flash.action_failed')),
                                    })"
                                    x-show="!deleted"
                                    x-transition.opacity.duration.150ms
                                    x-on:comment:deleted.window="if ($event.detail.id === id) deleted = true"
                                    class="group space-y-1 p-5 sm:p-6"
                                    :class="{
                                        'bg-orange-50/70': hidden,
                                        'opacity-60': isProcessing,
                                    }"
                                    :data-hidden-comment="hidden ? 'true' : null"
                                >
                                    {{-- En-tête : auteur à gauche, actions + Répondre à droite --}}
                                    <div class="flex items-start justify-between gap-3">
                                        <p class="text-xs text-zinc-500">
                                            {{ $comment->user->name }} • {{ $commentPublishedLabel }}
                                        </p>
                                        <div class="flex items-center gap-2">
                                            @auth
                                                <button
                                                    type="button"
                                                    class="inline-flex h-8 items-center gap-1 border px-2 text-xs font-medium transition-colors"
                                                    :class="showReply
                                                        ? 'border-brand-300 bg-brand-50 text-brand-700 hover:border-brand-400 hover:bg-brand-100'
                                                        : 'border-zinc-200 text-zinc-500 hover:border-zinc-300 hover:text-zinc-700'"
                                                    x-on:click="showReply = !showReply"
                                                    :aria-expanded="showReply ? 'true' : 'false'"
                                                    title="{{ __('ui.blog.comments.reply') }}"
                                                >
                                                    <x-ui.icon name="corner-down-right" class="size-3.5" />
                                                    <span>{{ __('ui.blog.comments.reply') }}</span>
                                                </button>
                                            @endauth
                                            @can('update', $comment)
                                                <form method="POST" action="{{ route('comments.update', ['comment' => $comment]) }}" x-on:submit.prevent="toggleVisibility($event)">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="is_hidden" x-bind:value="hidden ? 0 : 1">
                                                    <button
                                                        type="submit"
                                                        class="inline-flex h-8 items-center justify-center gap-1 border border-orange-200 bg-orange-50 px-2 text-xs font-medium text-orange-700 transition-colors hover:border-orange-300 hover:bg-orange-100 hover:text-orange-800"
                                                        x-bind:title="hidden ? shownLabel : hiddenLabel"
                                                        x-bind:disabled="isProcessing"
                                                    >
                                                        <span x-show="!hidden">
                                                            <x-ui.icon name="eye-off" class="size-4" />
                                                        </span>
                                                        <span x-show="hidden">
                                                            <x-ui.icon name="eye" class="size-4" />
                                                        </span>
                                                        <span x-text="hidden ? shownLabel : hiddenLabel"></span>
                                                    </button>
                                                </form>
                                            @endcan
                                            @can('delete', $comment)
                                                <button
                                                    type="button"
                                                    class="inline-flex size-8 items-center justify-center border border-zinc-300 text-zinc-500 transition-colors hover:border-red-400 hover:text-red-600"
                                                    title="{{ __('ui.blog.comments.delete') }}"
                                                    x-bind:disabled="isProcessing"
                                                    data-test="open-comment-delete-confirmation"
                                                    x-on:click="$dispatch('comment:request-delete', { url: '{{ route('comments.destroy', ['comment' => $comment]) }}', commentId: @js($comment->id) })"
                                                >
                                                    <x-ui.icon name="trash" class="size-4" />
                                                    <span class="sr-only">{{ __('ui.blog.comments.delete') }}</span>
                                                </button>
                                            @endcan
                                        </div>
                                    </div>

                                    {{-- Corps du commentaire --}}
                                    <div class="article-content max-w-none font-sans text-base">
                                        {!! $renderedComments[$comment->id] !!}
                                    </div>

                                    {{-- Formulaire inline de réponse --}}
                                    @auth
                                        <div
                                            x-show="showReply"
                                            x-transition:enter="transition ease-out duration-150"
                                            x-transition:enter-start="opacity-0 -translate-y-1"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            x-transition:leave="transition ease-in duration-100"
                                            x-transition:leave-start="opacity-100 translate-y-0"
                                            x-transition:leave-end="opacity-0 -translate-y-1"
                                            class="mt-3 border-l-2 border-zinc-400 pl-4"
                                        >
                                            <p class="mb-2 text-xs font-medium text-zinc-500">
                                                {{ __('ui.blog.comments.reply_to', ['name' => $comment->user->name]) }}
                                            </p>
                                            <form
                                                method="POST"
                                                action="{{ route('content.comments.store', ['contentItem' => $contentItem]) }}"
                                                class="space-y-2"
                                                x-data="{ submitting: false }"
                                                x-on:submit="submitting = true"
                                            >
                                                @csrf
                                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                <textarea
                                                    name="body_markdown"
                                                    rows="3"
                                                    class="w-full border border-zinc-300 px-3 py-2 text-sm focus:border-zinc-500 focus:outline-none"
                                                    placeholder="{{ __('ui.blog.comments.form_placeholder') }}"
                                                    aria-label="{{ __('ui.blog.comments.reply_to', ['name' => $comment->user->name]) }}"
                                                    x-ref="replyTextarea"
                                                    x-effect="if (showReply) $nextTick(() => $refs.replyTextarea?.focus())"
                                                    x-on:keydown.ctrl.enter.prevent="$el.closest('form').requestSubmit()"
                                                    x-on:keydown.meta.enter.prevent="$el.closest('form').requestSubmit()"
                                                    x-on:keydown.escape="showReply = false"
                                                ></textarea>
                                                @error('body_markdown')
                                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                                <div class="flex items-center gap-3">
                                                    <button
                                                        type="submit"
                                                        class="inline-flex h-8 items-center gap-2 border border-brand-700 px-3 text-xs font-medium text-brand-700 transition-colors hover:bg-brand-50 disabled:opacity-50"
                                                        :disabled="submitting"
                                                    >
                                                        <span x-show="submitting">
                                                            <svg class="size-3.5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                                                        </span>
                                                        {{ __('ui.blog.comments.publish') }}
                                                    </button>
                                                    <button
                                                        type="button"
                                                        class="h-8 border border-zinc-300 px-3 text-xs font-medium text-zinc-600 transition-colors hover:bg-zinc-100"
                                                        x-on:click="showReply = false"
                                                    >
                                                        {{ __('ui.blog.comments.cancel_reply') }}
                                                    </button>
                                                    <p class="hidden text-xs text-zinc-400 sm:block">
                                                        <kbd class="rounded border border-zinc-300 bg-zinc-100 px-1 py-0.5 font-mono text-xs">Ctrl</kbd>+<kbd class="rounded border border-zinc-300 bg-zinc-100 px-1 py-0.5 font-mono text-xs">↵</kbd>
                                                        &nbsp;·&nbsp;
                                                        <kbd class="rounded border border-zinc-300 bg-zinc-100 px-1 py-0.5 font-mono text-xs">Esc</kbd> {{ __('ui.blog.comments.shortcut_cancel') }}
                                                    </p>
                                                </div>
                                            </form>
                                        </div>
                                    @endauth

                                    {{-- Replies imbriquées (1 niveau) --}}
                                    @if ($comment->replies->isNotEmpty())
                                        <div class="mt-4 space-y-0 border-l-2 border-zinc-300">
                                            @foreach ($comment->replies as $reply)
                                                @php
                                                    $replyPublishedAt = $reply->created_at;
                                                    $replyPublishedLabel = '';
                                                    if ($replyPublishedAt !== null) {
                                                        $replyPublishedLabel = $replyPublishedAt->diffInSeconds(now()) < 60
                                                            ? __('ui.blog.comments.just_now')
                                                            : $replyPublishedAt->locale(app()->getLocale())->diffForHumans();
                                                    }
                                                    $replyDeleteModal = 'confirm-comment-deletion-'.$reply->id;
                                                @endphp
                                                <article
                                                    x-data="commentActions({
                                                        hidden: @js((bool) $reply->is_hidden),
                                                        id: @js($reply->id),
                                                        hiddenLabel: @js(__('ui.blog.comments.hide')),
                                                        shownLabel: @js(__('ui.blog.comments.show')),
                                                        hiddenToast: @js(__('ui.flash.comment_hidden')),
                                                        shownToast: @js(__('ui.flash.comment_shown')),
                                                        failedToast: @js(__('ui.flash.action_failed')),
                                                    })"
                                                    x-show="!deleted"
                                                    x-transition.opacity.duration.150ms
                                                    x-on:comment:deleted.window="if ($event.detail.id === id) deleted = true"
                                                    class="border-t border-zinc-200 pl-4 -mr-5 sm:-mr-6 pr-5 sm:pr-6 py-3 first:border-t-0"
                                                    :class="{
                                                        'bg-orange-50/70': hidden,
                                                        'opacity-60': isProcessing,
                                                    }"
                                                >
                                                    {{-- En-tête reply : indicateur ↳ à gauche, actions admin à droite --}}
                                                    <div class="flex items-start justify-between gap-3">
                                                        <p class="flex items-center gap-1 text-xs text-zinc-400">
                                                            <x-ui.icon name="corner-down-right" class="size-3 shrink-0 text-zinc-300" />
                                                            <span class="font-medium text-zinc-500">{{ $reply->user->name }}</span>
                                                            <span class="text-zinc-300">•</span>
                                                            <span>{{ $replyPublishedLabel }}</span>
                                                        </p>
                                                        <div class="flex items-center gap-2">
                                                            @can('update', $reply)
                                                                <form method="POST" action="{{ route('comments.update', ['comment' => $reply]) }}" x-on:submit.prevent="toggleVisibility($event)">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <input type="hidden" name="is_hidden" x-bind:value="hidden ? 0 : 1">
                                                                    <button
                                                                        type="submit"
                                                                        class="inline-flex h-7 items-center gap-1 border border-orange-200 bg-orange-50 px-2 text-xs font-medium text-orange-700 transition-colors hover:border-orange-300 hover:bg-orange-100"
                                                                        x-bind:title="hidden ? shownLabel : hiddenLabel"
                                                                        x-bind:disabled="isProcessing"
                                                                    >
                                                                        <span x-show="!hidden"><x-ui.icon name="eye-off" class="size-3.5" /></span>
                                                                        <span x-show="hidden"><x-ui.icon name="eye" class="size-3.5" /></span>
                                                                        <span x-text="hidden ? shownLabel : hiddenLabel"></span>
                                                                    </button>
                                                                </form>
                                                            @endcan
                                                            @can('delete', $reply)
                                                                <button
                                                                    type="button"
                                                                    class="inline-flex size-7 items-center justify-center border border-zinc-300 text-zinc-400 transition-colors hover:border-red-400 hover:text-red-600"
                                                                    title="{{ __('ui.blog.comments.delete') }}"
                                                                    x-bind:disabled="isProcessing"
                                                                    x-on:click="$dispatch('comment:request-delete', { url: '{{ route('comments.destroy', ['comment' => $reply]) }}', commentId: @js($reply->id) })"
                                                                >
                                                                    <x-ui.icon name="trash" class="size-3.5" />
                                                                    <span class="sr-only">{{ __('ui.blog.comments.delete') }}</span>
                                                                </button>
                                                            @endcan
                                                        </div>
                                                    </div>
                                                    {{-- Corps de la réponse --}}
                                                    <div class="mt-1 article-content max-w-none font-sans text-sm">
                                                        {!! $renderedComments[$reply->id] ?? '' !!}
                                                    </div>
                                                </article>
                                            @endforeach
                                        </div>
                                    @endif
                                </article>
                            @endforeach
                            </div>
                        @endif
                    </x-ui.card>

                    {{-- Modal global de confirmation de suppression (hors des articles, évite le freeze) --}}
                    @if ($comments->isNotEmpty())
                        @can('delete', $comments->first())
                            <flux:modal
                                name="confirm-comment-delete"
                                class="max-w-md"
                                x-on:comment:request-delete.window="openDeleteModal($event.detail.url, $event.detail.commentId)"
                            >
                                <div class="space-y-4">
                                    <flux:heading size="lg">{{ __('ui.blog.comments.confirm_delete_title') }}</flux:heading>
                                    <flux:text>{{ __('ui.blog.comments.confirm_delete_body') }}</flux:text>
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:modal.close>
                                            <flux:button variant="filled">{{ __('ui.blog.comments.confirm_delete_cancel') }}</flux:button>
                                        </flux:modal.close>
                                        <flux:button
                                            variant="danger"
                                            type="button"
                                            x-on:click="confirmDelete()"
                                            x-bind:disabled="deleteModal.isProcessing"
                                            data-test="confirm-comment-delete-button"
                                        >
                                            <span x-show="deleteModal.isProcessing">
                                                <svg class="size-4 animate-spin inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                                            </span>
                                            {{ __('ui.blog.comments.confirm_delete_confirm') }}
                                        </flux:button>
                                    </div>
                                </div>
                            </flux:modal>
                        @endcan
                    @endif

                    @auth
                        <x-ui.card>
                            <form
                                method="POST"
                                action="{{ route('content.comments.store', ['contentItem' => $contentItem]) }}"
                                class="space-y-3"
                                x-data="{ submitting: false }"
                                x-on:submit="submitting = true"
                            >
                                @csrf
                                <textarea
                                    id="body_markdown"
                                    name="body_markdown"
                                    rows="5"
                                    aria-label="{{ __('ui.blog.comments.form_placeholder') }}"
                                    class="w-full border border-zinc-300 px-3 py-2 text-sm focus:border-zinc-500 focus:outline-none"
                                    placeholder="{{ __('ui.blog.comments.form_placeholder') }}"
                                    x-on:keydown.ctrl.enter.prevent="$el.closest('form').requestSubmit()"
                                    x-on:keydown.meta.enter.prevent="$el.closest('form').requestSubmit()"
                                >{{ old('body_markdown') }}</textarea>
                                @error('body_markdown')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <div class="flex items-center gap-4">
                                    <button
                                        type="submit"
                                        class="inline-flex h-10 items-center gap-2 border border-brand-700 px-4 text-sm font-medium text-brand-700 transition-colors hover:bg-brand-50 focus:border-brand-800 focus-visible:border-brand-800 disabled:opacity-50"
                                        :disabled="submitting"
                                    >
                                        <span x-show="submitting">
                                            <svg class="size-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                                        </span>
                                        {{ __('ui.blog.comments.publish') }}
                                    </button>
                                    <p class="hidden text-xs text-zinc-400 sm:block">
                                        <kbd class="rounded border border-zinc-300 bg-zinc-100 px-1.5 py-0.5 font-mono text-xs">Ctrl</kbd>
                                        <span class="mx-0.5">+</span>
                                        <kbd class="rounded border border-zinc-300 bg-zinc-100 px-1.5 py-0.5 font-mono text-xs">↵</kbd>
                                        {{ __('ui.blog.comments.shortcut_submit') }}
                                    </p>
                                </div>
                            </form>
                        </x-ui.card>
                    @else
                        <x-ui.alert>{{ __('ui.blog.comments.login') }}</x-ui.alert>
                    @endauth
                </section>
            @endif

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const copyButtons = document.querySelectorAll('[data-copy-link]');

            const copyToClipboard = async (value) => {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(value);

                    return true;
                }

                const textarea = document.createElement('textarea');
                textarea.value = value;
                textarea.setAttribute('readonly', '');
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';

                document.body.appendChild(textarea);
                textarea.focus();
                textarea.select();

                const isCopied = document.execCommand('copy');
                document.body.removeChild(textarea);

                return isCopied;
            };

            copyButtons.forEach((button) => {
                button.addEventListener('click', async () => {
                    const url = button.dataset.copyLink ?? '';

                    if (url === '') {
                        return;
                    }

                    const defaultLabel = button.dataset.labelDefault ?? '';
                    const successLabel = button.dataset.labelSuccess ?? defaultLabel;
                    const errorLabel = button.dataset.labelError ?? defaultLabel;
                    const labelNode = button.querySelector('[data-copy-label]');

                    let isCopied = false;

                    try {
                        isCopied = await copyToClipboard(url);
                    } catch (error) {
                        isCopied = false;
                    }

                    if (labelNode instanceof HTMLElement) {
                        labelNode.textContent = isCopied ? successLabel : errorLabel;

                        window.setTimeout(() => {
                            labelNode.textContent = defaultLabel;
                        }, 1800);
                    }

                    if (isCopied) {
                        const dropdown = button.closest('details');

                        if (dropdown instanceof HTMLDetailsElement) {
                            dropdown.removeAttribute('open');
                        }
                    }
                });
            });
        });

        document.addEventListener('alpine:init', () => {
            Alpine.data('commentSection', (config) => ({
                deleteModal: {
                    url: '',
                    commentId: null,
                    isProcessing: false,
                },
                deletedToast: String(config.deletedToast ?? ''),
                failedToast: String(config.failedToast ?? ''),

                openDeleteModal(url, commentId) {
                    this.deleteModal.url = url;
                    this.deleteModal.commentId = commentId;
                    this.deleteModal.isProcessing = false;
                    window.dispatchEvent(new CustomEvent('modal-show', { detail: { name: 'confirm-comment-delete' } }));
                },

                async confirmDelete() {
                    if (this.deleteModal.isProcessing || !this.deleteModal.url) return;
                    this.deleteModal.isProcessing = true;
                    try {
                        const csrfToken = document.querySelector('meta[name=csrf-token]')?.content ?? '';
                        const resp = await fetch(this.deleteModal.url, {
                            method: 'POST',
                            headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
                            body: (() => {
                                const fd = new FormData();
                                fd.append('_method', 'DELETE');
                                fd.append('_token', csrfToken);
                                return fd;
                            })(),
                        });
                        if (!resp.ok) throw new Error();
                        const id = this.deleteModal.commentId;
                        window.dispatchEvent(new CustomEvent('modal-close', { detail: { name: 'confirm-comment-delete' } }));
                        window.dispatchEvent(new CustomEvent('comment:deleted', { detail: { id } }));
                        window.dispatchEvent(new CustomEvent('ui-toast', {
                            detail: { message: this.deletedToast, variant: 'success' },
                        }));
                    } catch (e) {
                        window.dispatchEvent(new CustomEvent('ui-toast', {
                            detail: { message: this.failedToast, variant: 'error' },
                        }));
                    } finally {
                        this.deleteModal.isProcessing = false;
                    }
                },
            }));

            Alpine.data('commentActions', (config) => ({
                hidden: Boolean(config.hidden),
                id: config.id ?? null,
                deleted: false,
                isProcessing: false,
                showReply: false,
                hiddenLabel: String(config.hiddenLabel ?? ''),
                shownLabel: String(config.shownLabel ?? ''),
                hiddenToast: String(config.hiddenToast ?? ''),
                shownToast: String(config.shownToast ?? ''),
                failedToast: String(config.failedToast ?? ''),

                notify(message, variant = 'success') {
                    if (message.trim() === '') return;
                    window.dispatchEvent(new CustomEvent('ui-toast', {
                        detail: { message, variant },
                    }));
                },

                async submitForm(form) {
                    if (!(form instanceof HTMLFormElement) || this.isProcessing) {
                        return false;
                    }

                    this.isProcessing = true;

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                Accept: 'application/json',
                            },
                            body: new FormData(form),
                        });

                        if (!response.ok) {
                            this.notify(this.failedToast, 'error');
                            return false;
                        }

                        return true;
                    } catch (error) {
                        this.notify(this.failedToast, 'error');
                        return false;
                    } finally {
                        this.isProcessing = false;
                    }
                },

                async toggleVisibility(event) {
                    const form = event.target;
                    const nextHiddenState = !this.hidden;
                    const isSubmitted = await this.submitForm(form);

                    if (!isSubmitted) {
                        return;
                    }

                    this.hidden = nextHiddenState;
                    this.notify(nextHiddenState ? this.hiddenToast : this.shownToast, 'success');
                },
            }));
        });
    </script>
</x-layouts.public>
