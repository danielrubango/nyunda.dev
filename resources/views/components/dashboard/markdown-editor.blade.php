@props([
    'name',
    'label' => 'Contenu Markdown',
    'value' => '',
    'rows' => 12,
    'required' => false,
    'placeholder' => '',
])

@php
    $resolvedPlaceholder = str_replace('\n', "\n", (string) $placeholder);
@endphp

<div class="space-y-2" data-markdown-editor>
    <label class="block text-sm font-medium text-zinc-700">{{ $label }}</label>

    <textarea
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $resolvedPlaceholder }}"
        class="w-full rounded-sm border border-zinc-300 px-3 py-2 text-sm text-zinc-900"
        @if ($required) required @endif
        data-markdown-input
    >{{ $value }}</textarea>

    <p class="text-xs text-zinc-500">Astuce: la barre d'outils EasyMDE est disponible quand le script est charge.</p>
</div>

@once
    @push('scripts')
        <script>
            (() => {
                const stylesheetId = 'easymde-style';
                const stylesheetUrl = 'https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css';
                const scriptId = 'easymde-script';
                const scriptUrl = 'https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js';

                let loaderPromise = null;

                const ensureStylesheet = () => {
                    if (document.getElementById(stylesheetId)) {
                        return;
                    }

                    const link = document.createElement('link');
                    link.id = stylesheetId;
                    link.rel = 'stylesheet';
                    link.href = stylesheetUrl;
                    document.head.appendChild(link);
                };

                const ensureScript = () => {
                    if (window.EasyMDE) {
                        return Promise.resolve();
                    }

                    if (loaderPromise) {
                        return loaderPromise;
                    }

                    loaderPromise = new Promise((resolve, reject) => {
                        const existing = document.getElementById(scriptId);

                        if (existing) {
                            existing.addEventListener('load', () => resolve(), { once: true });
                            existing.addEventListener('error', () => reject(new Error('Unable to load EasyMDE.')), { once: true });
                            return;
                        }

                        const script = document.createElement('script');
                        script.id = scriptId;
                        script.src = scriptUrl;
                        script.async = true;
                        script.onload = () => resolve();
                        script.onerror = () => reject(new Error('Unable to load EasyMDE.'));
                        document.head.appendChild(script);
                    });

                    return loaderPromise;
                };

                const initializeEditors = () => {
                    if (!window.EasyMDE) {
                        return;
                    }

                    document.querySelectorAll('[data-markdown-editor]').forEach((editor) => {
                        const textarea = editor.querySelector('[data-markdown-input]');

                        if (!textarea || textarea.dataset.easymdeInitialized === 'true') {
                            return;
                        }

                        textarea.dataset.easymdeInitialized = 'true';

                        new window.EasyMDE({
                            element: textarea,
                            spellChecker: false,
                            forceSync: true,
                            status: ['lines', 'words'],
                            toolbar: [
                                'bold',
                                'italic',
                                'heading',
                                '|',
                                'quote',
                                'unordered-list',
                                'ordered-list',
                                '|',
                                'link',
                                'image',
                                'code',
                                'preview',
                                'side-by-side',
                                'fullscreen',
                                '|',
                                'guide',
                            ],
                        });
                    });
                };

                document.addEventListener('DOMContentLoaded', () => {
                    ensureStylesheet();

                    ensureScript()
                        .then(() => initializeEditors())
                        .catch(() => {
                        });
                });
            })();
        </script>
    @endpush
@endonce
