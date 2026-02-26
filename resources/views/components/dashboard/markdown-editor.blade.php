@props([
    'name',
    'label' => 'Contenu Markdown',
    'value' => '',
    'rows' => 12,
    'required' => false,
    'placeholder' => '',
])

<div class="space-y-2" data-markdown-editor>
    <label class="block text-sm font-medium text-zinc-700">{{ $label }}</label>

    <div class="flex flex-wrap gap-2 rounded-sm border border-zinc-300 bg-zinc-50 p-2">
        <button type="button" class="inline-flex h-8 items-center border border-zinc-300 bg-white px-2 text-xs font-medium text-zinc-700" data-markdown-wrap="**" title="Gras">Gras</button>
        <button type="button" class="inline-flex h-8 items-center border border-zinc-300 bg-white px-2 text-xs font-medium text-zinc-700" data-markdown-wrap="*" title="Italique">Italique</button>
        <button type="button" class="inline-flex h-8 items-center border border-zinc-300 bg-white px-2 text-xs font-medium text-zinc-700" data-markdown-prefix="# " title="Titre">Titre</button>
        <button type="button" class="inline-flex h-8 items-center border border-zinc-300 bg-white px-2 text-xs font-medium text-zinc-700" data-markdown-prefix="- " title="Liste">Liste</button>
        <button type="button" class="inline-flex h-8 items-center border border-zinc-300 bg-white px-2 text-xs font-medium text-zinc-700" data-markdown-wrap="`" title="Code inline">Code</button>
        <button type="button" class="inline-flex h-8 items-center border border-zinc-300 bg-white px-2 text-xs font-medium text-zinc-700" data-markdown-block="```\n\n```" title="Bloc code">Bloc code</button>
        <button type="button" class="inline-flex h-8 items-center border border-zinc-300 bg-white px-2 text-xs font-medium text-zinc-700" data-markdown-link="[Texte](https://)" title="Lien">Lien</button>
    </div>

    <textarea
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        class="w-full rounded-sm border border-zinc-300 px-3 py-2 text-sm text-zinc-900"
        @if ($required) required @endif
        data-markdown-input
    >{{ $value }}</textarea>

    <p class="text-xs text-zinc-500">Astuce: utilisez la toolbar pour inserer rapidement les syntaxes Markdown.</p>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-markdown-editor]').forEach((editor) => {
                    const textarea = editor.querySelector('[data-markdown-input]');

                    if (!textarea) {
                        return;
                    }

                    const applyWrap = (wrapper) => {
                        const start = textarea.selectionStart;
                        const end = textarea.selectionEnd;
                        const selected = textarea.value.slice(start, end) || 'texte';
                        const result = `${wrapper}${selected}${wrapper}`;
                        textarea.setRangeText(result, start, end, 'end');
                        textarea.focus();
                    };

                    const applyPrefix = (prefix) => {
                        const start = textarea.selectionStart;
                        const end = textarea.selectionEnd;
                        const selected = textarea.value.slice(start, end) || 'texte';
                        const prefixed = selected
                            .split('\n')
                            .map((line) => `${prefix}${line}`)
                            .join('\n');

                        textarea.setRangeText(prefixed, start, end, 'end');
                        textarea.focus();
                    };

                    editor.querySelectorAll('[data-markdown-wrap]').forEach((button) => {
                        button.addEventListener('click', () => applyWrap(button.dataset.markdownWrap || '**'));
                    });

                    editor.querySelectorAll('[data-markdown-prefix]').forEach((button) => {
                        button.addEventListener('click', () => applyPrefix(button.dataset.markdownPrefix || '- '));
                    });

                    editor.querySelectorAll('[data-markdown-block]').forEach((button) => {
                        button.addEventListener('click', () => {
                            const template = button.dataset.markdownBlock || '```\n\n```';
                            const start = textarea.selectionStart;
                            const end = textarea.selectionEnd;
                            textarea.setRangeText(template, start, end, 'end');
                            textarea.focus();
                        });
                    });

                    editor.querySelectorAll('[data-markdown-link]').forEach((button) => {
                        button.addEventListener('click', () => {
                            const template = button.dataset.markdownLink || '[Texte](https://)';
                            const start = textarea.selectionStart;
                            const end = textarea.selectionEnd;
                            textarea.setRangeText(template, start, end, 'end');
                            textarea.focus();
                        });
                    });
                });
            });
        </script>
    @endpush
@endonce
