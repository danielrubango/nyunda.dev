<x-layouts.public :title="$title ?? null">
    <div class="ui-container">
        <div class="mx-auto w-full max-w-4xl">
            {{ $slot }}
        </div>
    </div>
</x-layouts.public>
