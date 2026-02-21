@props([
    'paginator',
])

@if ($paginator->hasPages())
    <nav aria-label="{{ __('ui.accessibility.pagination') }}" class="mt-8">
        {{ $paginator->onEachSide(1)->links() }}
    </nav>
@endif
