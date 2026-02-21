@props([
    'as' => 'div',
    'padding' => true,
])

<{{ $as }} {{ $attributes->class(['ui-card', 'ui-card-body' => $padding]) }}>
    {{ $slot }}
</{{ $as }}>
