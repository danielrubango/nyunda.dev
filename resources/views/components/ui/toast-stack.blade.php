@php
    $initialToasts = collect([
        session('status') ? ['message' => session('status'), 'variant' => 'success'] : null,
        session('error') ? ['message' => session('error'), 'variant' => 'error'] : null,
    ])->filter()->values();
@endphp

<div id="ui-toast-root" class="pointer-events-none fixed right-4 top-4 z-[100] flex w-full max-w-sm flex-col gap-3" aria-live="polite" aria-atomic="true"></div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toastRoot = document.getElementById('ui-toast-root');

        if (!(toastRoot instanceof HTMLElement) || toastRoot.dataset.initialized === '1') {
            return;
        }

        toastRoot.dataset.initialized = '1';

        const variants = {
            success: {
                wrapper: 'border-green-300 bg-green-50 text-green-900',
                button: 'border-green-300 text-green-700 hover:border-green-400 hover:text-green-900',
            },
            error: {
                wrapper: 'border-red-300 bg-red-50 text-red-900',
                button: 'border-red-300 text-red-700 hover:border-red-400 hover:text-red-900',
            },
            warning: {
                wrapper: 'border-amber-300 bg-amber-50 text-amber-900',
                button: 'border-amber-300 text-amber-700 hover:border-amber-400 hover:text-amber-900',
            },
            info: {
                wrapper: 'border-zinc-300 bg-white text-zinc-900',
                button: 'border-zinc-300 text-zinc-600 hover:border-zinc-400 hover:text-zinc-900',
            },
        };

        const createToast = (message, variant = 'info') => {
            if (typeof message !== 'string' || message.trim() === '') {
                return;
            }

            const style = variants[variant] ?? variants.info;
            const toast = document.createElement('div');
            toast.className = `pointer-events-auto relative border p-4 pr-12 text-sm shadow-sm transition-all duration-200 ${style.wrapper}`;
            toast.setAttribute('role', 'status');

            const messageNode = document.createElement('p');
            messageNode.className = 'm-0 leading-6';
            messageNode.textContent = message;
            toast.appendChild(messageNode);

            const closeButton = document.createElement('button');
            closeButton.type = 'button';
            closeButton.className = `absolute right-2 top-2 inline-flex size-7 items-center justify-center border text-base leading-none transition-colors ${style.button}`;
            closeButton.setAttribute('aria-label', 'Dismiss');
            closeButton.textContent = '×';
            closeButton.addEventListener('click', () => {
                toast.remove();
            });
            toast.appendChild(closeButton);

            toastRoot.appendChild(toast);

            window.setTimeout(() => {
                toast.classList.add('opacity-0');
                window.setTimeout(() => toast.remove(), 180);
            }, 4200);
        };

        const initialToasts = @js($initialToasts);
        initialToasts.forEach((toast) => {
            createToast(toast.message ?? '', toast.variant ?? 'info');
        });

        window.addEventListener('ui-toast', (event) => {
            const payload = Array.isArray(event.detail) ? event.detail[0] ?? {} : event.detail ?? {};
            createToast(payload.message ?? '', payload.variant ?? 'info');
        });
    });
</script>
