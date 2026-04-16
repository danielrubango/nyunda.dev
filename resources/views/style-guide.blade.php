@php
    $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
        items: collect(range(1, 10)),
        total: 30,
        perPage: 10,
        currentPage: (int) request()->query('page', 1),
        options: ['path' => route('style-guide')]
    );
@endphp

<x-layouts.public :seo="[
    'title' => 'Style Guide',
    'description' => 'Base components for the public interface.',
    'robots' => 'noindex,follow',
]">
    <div class="ui-container space-y-8">
        <header class="space-y-3">
            <p class="ui-eyebrow">UI</p>
            <h1 class="ui-section-title">Style Guide</h1>
            <p class="text-zinc-600">Base components for the public interface.</p>
        </header>

        <x-ui.card>
            <h2 class="text-lg font-semibold">Buttons</h2>
            <div class="mt-4 flex flex-wrap gap-2">
                <x-ui.button>Primary</x-ui.button>
                <x-ui.button variant="secondary">Secondary</x-ui.button>
                <x-ui.button variant="ghost">Ghost</x-ui.button>
                <x-ui.button variant="danger">Danger</x-ui.button>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-lg font-semibold">Badges</h2>
            <div class="mt-4 flex flex-wrap gap-2">
                <x-ui.badge variant="internal">Internal</x-ui.badge>
                <x-ui.badge variant="external">External</x-ui.badge>
                <x-ui.badge variant="community">Community</x-ui.badge>
                <x-ui.badge variant="success">Success</x-ui.badge>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-lg font-semibold">Inputs & Alerts</h2>
            <div class="mt-4 grid gap-3 md:grid-cols-2">
                <x-ui.input name="example" value="Input example" />
                <x-ui.alert variant="info">Informational alert</x-ui.alert>
                <x-ui.alert variant="success">Success alert</x-ui.alert>
                <x-ui.alert variant="error">Error alert</x-ui.alert>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-lg font-semibold">Tabs</h2>
            <x-ui.tabs
                class="mt-4"
                :items="[
                    ['label' => 'Overview', 'url' => route('style-guide'), 'active' => true],
                    ['label' => 'Content', 'url' => route('style-guide', ['tab' => 'content']), 'active' => false],
                    ['label' => 'Forum', 'url' => route('style-guide', ['tab' => 'forum']), 'active' => false],
                ]"
            />
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-lg font-semibold">Pagination</h2>
            <x-ui.pagination :paginator="$paginator" />
        </x-ui.card>
    </div>
</x-layouts.public>
